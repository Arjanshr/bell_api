<?php

use App\Http\Controllers\AiImageController;
use App\Http\Controllers\Admin\CKeditorController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\BankTenureController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BlogCategoryController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\GalleryController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CategoryBrandController;
use App\Http\Controllers\CategorySpecificationController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PopupBannerController;
use App\Http\Controllers\ProcessingFeeRuleController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Models\Brand;
use App\Http\Controllers\ProvinceController;
use App\Http\Controllers\CityController;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\OrderCancellationCategoryController;
use App\Http\Controllers\OrderCancellationController;
use App\Http\Controllers\QuestionsController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontController::class, 'home'])->name('front.home');
Route::get('/storage-link', function () {
    Artisan::call('storage:link');
    return 'storage:linked';
});

Route::post('/remove-bg', [ImageController::class, 'removeBackground']);

// Sitemap routes
Route::get('/sitemap.xml', [SitemapController::class, 'index']);
Route::get('/sitemaps/{type}.xml', [SitemapController::class, 'child']);

//
Route::middleware('guest')->group(function () {
    Route::get('auth/{provider}/redirect', [SocialiteController::class, 'loginSocial'])
        ->name('socialite.auth');
    Route::get('auth/{provider}/callback', [SocialiteController::class, 'callbackSocial'])
        ->name('socialite.callback');
});

Route::get('/categories/export', [CategoryController::class, 'export'])->name('category.export');

Route::get('admin/ajax/category-brands/{category}', function ($categoryId) {
    $brands = Brand::whereHas('products', function ($q) use ($categoryId) {
        $q->whereHas('categories', function ($q2) use ($categoryId) {
            $q2->where('categories.id', $categoryId);
        });
    })->select('id', 'name')->get();
    return response()->json($brands);
})->middleware(['auth:sanctum', 'is_admin']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'ensure-email-verified', // Use the custom middleware
    'is_admin',
    'ensure-user-active',
])->prefix('admin')->group(function () {
    // Location management routes
    Route::prefix('locations')->name('locations.')->middleware('can:browse-locations')->group(function () {
        // Provinces
        Route::get('/provinces', [ProvinceController::class, 'index'])->name('provinces.index');
        Route::get('/provinces/create', [ProvinceController::class, 'create'])->name('provinces.create');
        Route::post('/provinces', [ProvinceController::class, 'store'])->name('provinces.store');
        Route::get('/provinces/edit/{province}', [ProvinceController::class, 'edit'])->name('provinces.edit');
        Route::patch('/provinces/edit/{province}', [ProvinceController::class, 'update'])->name('provinces.update');
        Route::delete('/provinces/delete/{province}', [ProvinceController::class, 'destroy'])->name('provinces.delete');

        // Cities
        Route::get('/cities', [CityController::class, 'index'])->name('cities.index');
        Route::get('/cities/create', [CityController::class, 'create'])->name('cities.create');
        Route::post('/cities', [CityController::class, 'store'])->name('cities.store');
        Route::get('/cities/edit/{city}', [CityController::class, 'edit'])->name('cities.edit');
        Route::patch('/cities/edit/{city}', [CityController::class, 'update'])->name('cities.update');
        Route::delete('/cities/delete/{city}', [CityController::class, 'destroy'])->name('cities.delete');

        // Areas
        Route::get('/areas', [AreaController::class, 'index'])->name('areas.index');
        Route::get('/areas/create', [AreaController::class, 'create'])->name('areas.create');
        Route::post('/areas', [AreaController::class, 'store'])->name('areas.store');
        Route::post('/areas/mass-update-price', [AreaController::class, 'massUpdatePrice'])->name('areas.massUpdatePrice');
        Route::post('/areas/mass-delete', [AreaController::class, 'massDelete'])->name('areas.massDelete');
        Route::get('/areas/edit/{area}', [AreaController::class, 'edit'])->name('areas.edit');
        Route::patch('/areas/edit/{area}', [AreaController::class, 'update'])->name('areas.update');
        Route::delete('/areas/delete/{area}', [AreaController::class, 'destroy'])->name('areas.delete');
    });
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/test', [DashboardController::class, 'test'])->name('admin.test');

    //Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //Users routes
    Route::get('/users', [UserController::class, 'index'])->name('users')->middleware('can:browse-users');
    Route::get('/users/export', [UserController::class, 'export'])->name('user.export')->middleware('can:browse-users');
    Route::middleware('can:add-users')->group(function () {
        Route::get('/users/create', [UserController::class, 'create'])->name('user.create');
        Route::post('/users/insert', [UserController::class, 'insert'])->name('user.insert');
    });
    Route::get('/users/{user}', [UserController::class, 'show'])->name('user.show')->middleware('can:read-users');
    Route::middleware('can:edit-users')->group(function () {
        Route::get('/users/edit/{user}', [UserController::class, 'edit'])->name('user.edit');
        Route::patch('/users/edit/{user}', [UserController::class, 'update'])->name('user.update');
    });
    Route::delete('/users/delete/{user}', [UserController::class, 'delete'])->name('user.delete')->middleware('can:delete-users');
    Route::get('/users/activities/{user}', [UserController::class, 'activities'])->name('user.activity')->middleware('can:browse-activities');
    Route::get('/users/activities/view/{activity}', [UserController::class, 'showActivity'])->name('user.activity.show')->middleware('can:read-activities');
    Route::patch('/user/{user}/deactivate', [UserController::class, 'deactivate'])->name('user.deactivate');
    Route::patch('/user/{user}/activate', [UserController::class, 'activate'])->name('user.activate');


    Route::get('/vendors', [UserController::class, 'vendors'])->name('vendors.index')->middleware('can:read-users');

    // Vendor routes
    Route::middleware('can:browse-vendors')->group(function () {
        Route::get('/vendors', [UserController::class, 'vendors'])->name('vendors.index');
        Route::middleware('can:add-vendors')->group(function () {
            Route::get('/vendors/create', [UserController::class, 'create'])->name('vendor.create');
            Route::post('/vendors/insert', [UserController::class, 'insert'])->name('vendor.insert');
        });
        Route::get('/vendors/{user}', [UserController::class, 'show'])->name('vendor.show')->middleware('can:read-vendors');
        Route::middleware('can:edit-vendors')->group(function () {
            Route::get('/vendors/edit/{user}', [UserController::class, 'edit'])->name('vendor.edit');
            Route::patch('/vendors/edit/{user}', [UserController::class, 'update'])->name('vendor.update');
        });
        Route::delete('/vendors/delete/{user}', [UserController::class, 'delete'])->name('vendor.delete')->middleware('can:delete-vendors');
        Route::patch('/vendor/{user}/deactivate', [UserController::class, 'deactivate'])->name('vendor.deactivate');
        Route::patch('/vendor/{user}/activate', [UserController::class, 'activate'])->name('vendor.activate');
    });

    Route::middleware('can:read-activities')->group(function () {
        Route::get('/vendors/activities/{user}', [UserController::class, 'activities'])->name('vendor.activity');
        Route::get('/vendors/activities/view/{activity}', [UserController::class, 'showActivity'])->name('vendor.activity.show');
    });

    //Roles routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles')->middleware('can:browse-roles');
    Route::middleware('can:add-roles')->group(function () {
        Route::get('/roles/create', [RoleController::class, 'create'])->name('role.create');
        Route::post('/roles/insert', [RoleController::class, 'insert'])->name('role.insert');
    });
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('role.show')->middleware('can:read-roles');
    Route::middleware('can:edit-roles')->group(function () {
        Route::get('/roles/edit/{role}', [RoleController::class, 'edit'])->name('role.edit');
        Route::patch('/roles/edit/{role}', [RoleController::class, 'update'])->name('role.update');
    });
    Route::delete('/roles/delete/{role}', [RoleController::class, 'delete'])->name('role.delete')->middleware('can:delete-roles');


    //Permission routes
    Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions')->middleware('can:browse-permissions');
    Route::middleware('can:add-permissions')->group(function () {
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permission.create');
        Route::post('/permissions/insert', [PermissionController::class, 'insert'])->name('permission.insert');
    });
    Route::get('/permissions/{permission}', [PermissionController::class, 'show'])->name('permission.show')->middleware('can:read-permissions');
    Route::middleware('can:edit-permissions')->group(function () {
        Route::get('/permissions/edit/{permission}', [PermissionController::class, 'edit'])->name('permission.edit');
        Route::patch('/permissions/edit/{permission}', [PermissionController::class, 'update'])->name('permission.update');
    });
    Route::delete('/permissions/delete/{permission}', [PermissionController::class, 'delete'])->name('permission.delete')->middleware('can:delete-permissions');

    Route::prefix('settings')->name('setting.')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('index')->middleware('can:browse-settings');

        Route::middleware('can:edit-general-settings')->group(function () {
            Route::get('/general', [SettingController::class, 'generalSettings'])->name('general.edit');
            Route::patch('/general', [SettingController::class, 'updateGeneralSettings'])->name('general.update');
        });

        Route::middleware('can:add-settings')->group(function () {
            Route::get('/create', [SettingController::class, 'create'])->name('create');
            Route::post('/insert', [SettingController::class, 'insert'])->name('insert');
        });

        Route::get('/{setting}', [SettingController::class, 'show'])->name('show')->middleware('can:read-settings');

        Route::middleware('can:edit-settings')->group(function () {
            Route::get('/edit/{setting}', [SettingController::class, 'edit'])->name('edit');
            Route::patch('/edit/{setting}', [SettingController::class, 'update'])->name('update');
        });

        Route::delete('/delete/{setting}', [SettingController::class, 'delete'])->name('delete')->middleware('can:delete-settings');
    });

    //Categories routes
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories')->middleware('can:browse-categories');
    Route::middleware('can:add-categories')->group(function () {
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('category.create');
        Route::post('/categories/insert', [CategoryController::class, 'insert'])->name('category.insert');
    });
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('category.show')->middleware('can:read-categories');
    Route::middleware('can:edit-categories')->group(function () {
        Route::get('/categories/edit/{category}', [CategoryController::class, 'edit'])->name('category.edit');
        Route::patch('/categories/edit/{category}', [CategoryController::class, 'update'])->name('category.update');
    });
    Route::delete('/categories/delete/{category}', [CategoryController::class, 'delete'])->name('category.delete')->middleware('can:delete-categories');

    //Category Specifications routes
    Route::get('/category-specifications/{category}', [CategoryController::class, 'categorySpecifications'])->name('category-specifications')->middleware('can:browse-category-specifications');
    Route::middleware('can:add-category-specifications')->group(function () {
        Route::get('/category-specifications/create/{category}', [CategoryController::class, 'createCategorySpecifications'])->name('category-specification.create');
        Route::post('/category-specifications/insert/{category}', [CategoryController::class, 'insertCategorySpecifications'])->name('category-specification.insert');
    });
    Route::middleware('can:edit-category-specifications')->group(function () {
        Route::get('/category-specifications/edit/{category}/{category_specification}', [CategoryController::class, 'editCategorySpecifications'])->name('category-specification.edit');
        Route::patch('/category-specifications/edit/{category}/{category_specification}', [CategoryController::class, 'updateCategorySpecifications'])->name('category-specification.update');
    });
    Route::delete('/category-specifications/delete/{category}/{category_specification_id}', [CategoryController::class, 'deleteCategorySpecifications'])->name('category-specification.delete')->middleware('can:delete-category-specifications');
    Route::post('categories/{category}/specifications/update-order', [CategoryController::class, 'updateOrder'])
        ->name('category-specification.updateOrder');

    //Brand routes
    Route::get('/brands', [BrandController::class, 'index'])->name('brands')->middleware('can:browse-brands');
    Route::middleware('can:add-brands')->group(function () {
        Route::get('/brands/create', [BrandController::class, 'create'])->name('brand.create');
        Route::post('/brands/insert', [BrandController::class, 'insert'])->name('brand.insert');
    });
    Route::get('/brands/{brand}', [BrandController::class, 'show'])->name('brand.show')->middleware('can:read-brands');
    Route::middleware('can:edit-brands')->group(function () {
        Route::get('/brands/edit/{brand}', [BrandController::class, 'edit'])->name('brand.edit');
        Route::patch('/brands/edit/{brand}', [BrandController::class, 'update'])->name('brand.update');
    });
    Route::delete('/brands/delete/{brand}', [BrandController::class, 'delete'])->name('brand.delete')->middleware('can:delete-brands');

    //Products routes
    Route::get('products/sync', [ProductController::class, 'syncProducts'])->name('product.sync');
    Route::get('products/export', [ProductController::class, 'export'])->name('product.export');
    Route::get('/products', [ProductController::class, 'index'])->name('products')->middleware('can:browse-products');
    Route::middleware('can:add-products')->group(function () {
        Route::get('/products/create', [ProductController::class, 'create'])->name('product.create');
        Route::post('/products/import', [ProductController::class, 'import'])->name('product.import');
        Route::post('/products/insert', [ProductController::class, 'insert'])->name('product.insert');
    });
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('product.show')->middleware('can:read-products');
    Route::middleware('can:edit-products')->group(function () {
        Route::get('/products/edit/{product}', [ProductController::class, 'edit'])->name('product.edit');
        Route::patch('/products/edit/{product}', [ProductController::class, 'update'])->name('product.update');

        Route::get('/products/specifications/{product}', [ProductController::class, 'manageSpecifications'])->name('product.specifications');
        Route::get('/products/specifications/create/{product}', [ProductController::class, 'createSpecifications'])->name('product.specification.create');
        Route::post('/products/specifications/insert/{product}', [ProductController::class, 'insertSpecifications'])->name('product.specification.insert');
        Route::get('/products/specifications/edit/{product_specification}', [ProductController::class, 'editSpecifications'])->name('product.specification.edit');
        Route::patch('/products/specifications/edit/{product_specification}', [ProductController::class, 'updateSpecifications'])->name('product.specification.update');
        Route::delete('/products/specifications/delete/{product}/{specification}', [ProductController::class, 'deleteSpecifications'])->name('product.specification.delete');
        Route::delete('/products/specifications/delete_all/{product}', [ProductController::class, 'deleteAllSpecifications'])->name('product.specification.delete.all');

        Route::get('/products/features/{product}', [ProductController::class, 'manageFeatures'])->name('product.features');
        Route::get('/products/features/create/{product}', [ProductController::class, 'createFeatures'])->name('product.feature.create');
        Route::post('/products/features/insert/{product}', [ProductController::class, 'insertFeatures'])->name('product.feature.insert');
        Route::get('/products/features/edit/{feature}', [ProductController::class, 'editFeatures'])->name('product.feature.edit');
        Route::patch('/products/features/edit/{feature}', [ProductController::class, 'updateFeatures'])->name('product.feature.update');
        Route::delete('/products/features/delete/{feature}', [ProductController::class, 'deleteFeatures'])->name('product.feature.delete');
        Route::delete('/products/features/delete_all/{product}', [ProductController::class, 'deleteAllFeatures'])->name('product.features.delete.all');

        Route::get('/products/images/link-images', [ProductController::class, 'linkImages']);
        Route::get('/products/images/{product}', [ProductController::class, 'manageImages'])->name('product.images');
        Route::post('/products/images/insert/{product}', [ProductController::class, 'insertImages'])->name('product.image.insert');
        Route::patch('/products/images/edit/{product}', [ProductController::class, 'updateImages'])->name('product.image.update');
        Route::delete('/products/images/delete/{product}', [ProductController::class, 'deleteImages'])->name('product.image.delete');

        Route::get('/products/variants/{product}', [ProductController::class, 'manageVariants'])->name('product.variants');
        Route::get('/products/variants/create/{product}', [ProductController::class, 'createVariants'])->name('product.variant.create');
        Route::post('/products/variants/insert/{product}', [ProductController::class, 'insertVariants'])->name('product.variant.insert');
        // Edit Variant
        Route::get('/products/{product}/variants/{variant}/edit', [ProductController::class, 'editVariants'])->name('product.variant.edit');

        // Update Variant
        Route::put('/products/{product}/variants/{variant}', [ProductController::class, 'updateVariants'])->name('product.variant.update');

        // Delete Variant
        Route::delete('/products/{product}/variants/{variant}', [ProductController::class, 'deleteVariants'])->name('product.variant.delete');
        Route::delete('/products/variants/delete_all/{product}', [ProductController::class, 'deleteAllVariants'])->name('product.variant.delete.all');
    });
    Route::delete('/products/delete/{product}', [ProductController::class, 'delete'])->name('product.delete')->middleware('can:delete-products');

    // Questions routes
    Route::get('/questions', [QuestionsController::class, 'index'])->name('questions');
    Route::get('/questions/{question}/answer', [QuestionsController::class, 'answer'])->name('questions.answer');
    Route::post('/questions/{question}/answer', [QuestionsController::class, 'submitAnswer'])->name('questions.answer.submit');
    Route::delete('/questions/{question}', [QuestionsController::class, 'delete'])->name('questions.delete');

    //Order routes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders')->middleware('can:browse-orders');
    Route::middleware('can:add-orders')->group(function () {
        Route::get('/orders/create', [OrderController::class, 'create'])->name('order.create');
        Route::post('/orders/insert', [OrderController::class, 'insert'])->name('order.insert');
    });
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('order.show')->middleware('can:read-orders');
    Route::middleware('can:edit-orders')->group(function () {
        Route::get('/orders/edit/{order}', [OrderController::class, 'edit'])->name('order.edit');
        Route::patch('/orders/edit/{order}', [OrderController::class, 'update'])->name('order.update');
    });
    Route::delete('/orders/delete/{order}', [OrderController::class, 'delete'])->name('order.delete')->middleware('can:delete-orders');

    // Order cancellation routes
    Route::get('/orders/{order}/cancel', [OrderCancellationController::class, 'create'])->name('order.cancel')->middleware('can:edit-orders');
    Route::post('/orders/{order}/cancel', [OrderCancellationController::class, 'store'])->name('order.cancel.store')->middleware('can:edit-orders');

    // Order cancellation categories (CRUD)
    Route::get('/order-cancellation-categories', [OrderCancellationCategoryController::class, 'index'])->name('order-cancellation-categories')->middleware('can:browse-settings');
    Route::get('/order-cancellation-categories/create', [OrderCancellationCategoryController::class, 'create'])->name('order-cancellation-categories.create')->middleware('can:add-settings');
    Route::post('/order-cancellation-categories', [OrderCancellationCategoryController::class, 'store'])->name('order-cancellation-categories.store')->middleware('can:add-settings');
    Route::get('/order-cancellation-categories/edit/{orderCancellationCategory}', [OrderCancellationCategoryController::class, 'edit'])->name('order-cancellation-categories.edit')->middleware('can:edit-settings');
    Route::patch('/order-cancellation-categories/{orderCancellationCategory}', [OrderCancellationCategoryController::class, 'update'])->name('order-cancellation-categories.update')->middleware('can:edit-settings');
    Route::delete('/order-cancellation-categories/delete/{orderCancellationCategory}', [OrderCancellationCategoryController::class, 'destroy'])->name('order-cancellation-categories.delete')->middleware('can:delete-settings');

    //Homepage content routes
    Route::get('/contents/{content_type}', [ContentController::class, 'index'])->name('contents')->middleware('can:browse-contents');
    Route::post('/contents/{content_type}/insert', [ContentController::class, 'insert'])->name('contents.insert')->middleware('can:add-contents');
    Route::delete('/contents/{content_type}/delete/{content_id}', [ContentController::class, 'delete'])->name('content.delete')->middleware('can:delete-contents');

    // Campaign Routes
    Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns')->middleware('can:browse-campaigns');
    Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create')->middleware('can:add-campaigns');
    Route::post('/campaigns/create', [CampaignController::class, 'insert'])->name('campaigns.insert')->middleware('can:add-campaigns');
    Route::get('/campaigns/edit/{campaign}', [CampaignController::class, 'edit'])->name('campaigns.edit')->middleware('can:edit-campaigns');
    Route::patch('/campaigns/edit/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update')->middleware('can:edit-campaigns');
    Route::get('/campaigns/products/{campaign}', [CampaignController::class, 'products'])->name('campaigns.products')->middleware('can:read-campaigns');
    Route::post('/campaigns/products/{campaign}', [CampaignController::class, 'productsAction'])->name('campaigns.products.action')->middleware('can:read-campaigns');
    Route::get('/campaigns/products/{campaign}/delete/{product}', [CampaignController::class, 'productDelete'])->name('campaigns.products.delete')->middleware('can:delete-campaigns');
    Route::delete('/campaigns/delete/{campaign}', [CampaignController::class, 'delete'])->name('campaigns.delete')->middleware('can:delete-campaigns');
    Route::post('/update-discount', [CampaignController::class, 'updateDiscount']);
    Route::post('/campaigns/update-order', [CampaignController::class, 'updateOrder'])->name('campaigns.updateOrder');

    //Sliders routes
    Route::get('/sliders', [SliderController::class, 'index'])->name('sliders')->middleware('can:browse-sliders');
    Route::middleware('can:add-sliders')->group(function () {
        Route::get('/sliders/create', [SliderController::class, 'create'])->name('slider.create');
        Route::post('/sliders/insert', [SliderController::class, 'insert'])->name('slider.insert');
    });
    Route::get('/sliders/{slider}', [SliderController::class, 'show'])->name('slider.show')->middleware('can:read-sliders');
    Route::middleware('can:edit-sliders')->group(function () {
        Route::get('/sliders/edit/{slider}', [SliderController::class, 'edit'])->name('slider.edit');
        Route::patch('/sliders/edit/{slider}', [SliderController::class, 'update'])->name('slider.update');
    });
    Route::delete('/sliders/delete/{slider}', [SliderController::class, 'delete'])->name('slider.delete')->middleware('can:delete-sliders');
    Route::post('/slider/update-order', [SliderController::class, 'updateOrder'])->name('slider.updateOrder');

    //Blogs routes
    Route::get('/blogs/export', [BlogController::class, 'export'])->name('blog.export')->middleware('can:browse-blogs');
    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs')->middleware('can:browse-blogs');
    Route::middleware('can:add-blogs')->group(function () {
        Route::get('/blogs/create', [BlogController::class, 'create'])->name('blog.create');
        Route::post('/blogs/insert', [BlogController::class, 'insert'])->name('blog.insert');
    });
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blog.show')->middleware('can:read-blogs');
    Route::middleware('can:edit-blogs')->group(function () {
        Route::get('/blogs/edit/{blog}', [BlogController::class, 'edit'])->name('blog.edit');
        Route::patch('/blogs/edit/{blog}', [BlogController::class, 'update'])->name('blog.update');
    });
    Route::delete('/blogs/delete/{blog}', [BlogController::class, 'delete'])->name('blog.delete')->middleware('can:delete-blogs');

    // Blog Category CRUD routes
    Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('blog-categories.index')->middleware('can:browse-blogs');
    Route::get('/blog-categories/create', [BlogCategoryController::class, 'create'])->name('blog-categories.create')->middleware('can:add-blogs');
    Route::post('/blog-categories', [BlogCategoryController::class, 'store'])->name('blog-categories.store')->middleware('can:add-blogs');
    Route::get('/blog-categories/{blogCategory}/edit', [BlogCategoryController::class, 'edit'])->name('blog-categories.edit')->middleware('can:edit-blogs');
    Route::patch('/blog-categories/{blogCategory}', [BlogCategoryController::class, 'update'])->name('blog-categories.update')->middleware('can:edit-blogs');
    Route::delete('/blog-categories/{blogCategory}', [BlogCategoryController::class, 'destroy'])->name('blog-categories.destroy')->middleware('can:delete-blogs');

    // Coupons routes
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons')->middleware('can:browse-coupons');
    Route::middleware('can:add-coupons')->group(function () {
        Route::get('/coupons/create', [CouponController::class, 'create'])->name('coupons.create');
        Route::post('/coupons/insert', [CouponController::class, 'insert'])->name('coupons.insert');
    });
    Route::get('/coupons/{coupon}', [CouponController::class, 'show'])->name('coupons.show');
    Route::middleware('can:edit-coupons')->group(function () {
        Route::get('/coupons/{coupon}/edit', [CouponController::class, 'edit'])->name('coupons.edit');
        Route::patch('/coupons/{coupon}', [CouponController::class, 'update'])->name('coupons.update');
    });
    Route::delete('/coupons/{coupon}', [CouponController::class, 'delete'])->name('coupons.delete')->middleware('can:delete-coupons');
    Route::get('/coupons/{coupon}/orders', [CouponController::class, 'orders'])->name('coupons.orders');

    // Popup Banner routes
    Route::get('/popup-banners', [PopupBannerController::class, 'index'])->name('popup-banners.index')->middleware('can:browse-popup-banners');
    Route::get('/popup-banners/edit/{popupBanner}', [PopupBannerController::class, 'edit'])->name('popup-banners.edit')->middleware('can:edit-popup-banners');
    Route::patch('/popup-banners/edit/{popupBanner}', [PopupBannerController::class, 'update'])->name('popup-banners.update');

    // routes/web.php or routes/admin.php
    Route::get('/search-report', [ReportController::class, 'searchReport'])->name('search-report')->middleware('can:read-reports');
    Route::get('/product-view-report', [ReportController::class, 'productViewReport'])->name('product-view-report')->middleware('can:read-reports');

    Route::get('/banks', [BankController::class, 'index'])->name('banks.index')->middleware('can:browse-banks');
    Route::get('/banks/create', [BankController::class, 'create'])->name('banks.create')->middleware('can:add-banks');
    Route::post('/banks/create', [BankController::class, 'insert'])->name('banks.insert')->middleware('can:add-banks');

    Route::middleware('can:edit-banks')->group(function () {
        Route::get('/banks/edit/{bank}', [BankController::class, 'edit'])->name('banks.edit');
        Route::patch('/banks/edit/{bank}', [BankController::class, 'update'])->name('banks.update');
    });

    Route::delete('/banks/delete/{bank}', [BankController::class, 'delete'])->name('banks.delete')->middleware('can:delete-banks');

    // Bank Tenures (use banks permissions)
    Route::get('/banks/tenures', [BankTenureController::class, 'index'])->name('banks-tenures.index')->middleware('can:browse-banks');
    Route::get('/banks/tenures/create', [BankTenureController::class, 'create'])->name('banks-tenures.create')->middleware('can:add-banks');
    Route::post('/banks/tenures', [BankTenureController::class, 'insert'])->name('banks-tenures.insert')->middleware('can:add-banks');

    Route::middleware('can:edit-banks')->group(function () {
        Route::get('/banks/tenures/edit/{bankTenure}', [BankTenureController::class, 'edit'])->name('banks-tenures.edit');
        Route::patch('/banks/tenures/edit/{bankTenure}', [BankTenureController::class, 'update'])->name('banks-tenures.update');
    });

    Route::delete('/banks/tenures/delete/{bankTenure}', [BankTenureController::class, 'delete'])->name('banks-tenures.delete')->middleware('can:delete-banks');

    // Processing Fee Rules (use banks permissions)
    Route::get('/banks/processing-fee-rules', [ProcessingFeeRuleController::class, 'index'])
        ->name('processing-fee-rules.index')->middleware('can:browse-banks');
    Route::middleware('can:add-banks')->group(function () {
        Route::get('/banks/processing-fee-rules/create', [ProcessingFeeRuleController::class, 'create'])->name('processing-fee-rules.create');
        Route::post('/banks/processing-fee-rules', [ProcessingFeeRuleController::class, 'store'])->name('processing-fee-rules.store');
    });
    Route::middleware('can:edit-banks')->group(function () {
        Route::get('/banks/processing-fee-rules/{processing_fee_rule}/edit', [ProcessingFeeRuleController::class, 'edit'])->name('processing-fee-rules.edit');
        Route::patch('/banks/processing-fee-rules/{processing_fee_rule}', [ProcessingFeeRuleController::class, 'update'])->name('processing-fee-rules.update');
    });
    Route::delete('/banks/processing-fee-rules/{processing_fee_rule}', [ProcessingFeeRuleController::class, 'destroy'])
        ->name('processing-fee-rules.destroy')->middleware('can:delete-banks');

    Route::get('/reviews', [DashboardController::class, 'allReviews'])->name('admin.reviews.index');
    Route::post('/admin/review/{id}/toggle-status', [ReviewController::class, 'toggleStatus'])
        ->name('admin.review.toggle-status')
        ->middleware('auth');

    //Ckeditor
    Route::post('/admin/blog/image-upload', [CKeditorController::class, 'blogImageUpload'])->name('blog.image.upload');
    Route::post('/admin/products/image-upload', [CKeditorController::class, 'productImageUpload'])->name('product.image.upload');
    Route::post('/admin/category-brand/image-upload', [CKeditorController::class, 'categoryBrandImageUpload'])
        ->name('category-brand.image.upload')
        ->middleware(['auth:sanctum', 'is_admin']);

    // Category-Brand summary/description routes
    Route::get('/category-brands', [CategoryBrandController::class, 'index'])
        ->name('category-brand.index')
        ->middleware('can:edit-categories');
    Route::get('/category-brands/create', [CategoryBrandController::class, 'create'])
        ->name('category-brand.create')
        ->middleware('can:edit-categories');
    Route::post('/category-brands', [CategoryBrandController::class, 'store'])
        ->name('category-brand.store')
        ->middleware('can:edit-categories');
    Route::get('/categories/{category}/brands/{brand}', [CategoryBrandController::class, 'edit'])
        ->name('category-brand.edit')
        ->middleware('can:edit-categories');
    Route::patch('/categories/{category}/brands/{brand}', [CategoryBrandController::class, 'update'])
        ->name('category-brand.update')
        ->middleware('can:edit-categories');
    Route::delete('/categories/{category}/brands/{brand}', [CategoryBrandController::class, 'delete'])
        ->name('category-brand.delete')
        ->middleware('can:edit-categories');

    // Gallery Management (controller + Blade)
    Route::get('/galleries', [GalleryController::class, 'index'])
        ->name('galleries')->middleware('can:browse-galleries');

    Route::get('/galleries/create', [GalleryController::class, 'create'])
        ->name('gallery.create')->middleware('can:add-galleries');
    Route::post('/galleries', [GalleryController::class, 'store'])
        ->name('gallery.store')->middleware('can:add-galleries');

    Route::get('/galleries/edit/{gallery}', [GalleryController::class, 'edit'])
        ->name('gallery.edit')->middleware('can:edit-galleries');
    Route::patch('/galleries/edit/{gallery}', [GalleryController::class, 'update'])
        ->name('gallery.update')->middleware('can:edit-galleries');
    Route::delete('/galleries/delete/{gallery}', [GalleryController::class, 'destroy'])
        ->name('gallery.delete')->middleware('can:delete-galleries');

    Route::get('/galleries/images/{gallery}', [GalleryController::class, 'images'])
        ->name('gallery.images')->middleware('can:edit-galleries');
    Route::post('/galleries/images/upload/{gallery}', [GalleryController::class, 'uploadImages'])
        ->name('gallery.image.insert')->middleware('can:edit-galleries');
    Route::patch('/galleries/images/{gallery_image}', [GalleryController::class, 'updateImage'])
        ->name('gallery.image.update')->middleware('can:edit-galleries');
    Route::post('/galleries/images/reorder/{gallery}', [GalleryController::class, 'reorderImages'])
        ->name('gallery.image.reorder')->middleware('can:edit-galleries');
    Route::delete('/galleries/images/delete/{gallery_image}', [GalleryController::class, 'deleteImage'])
        ->name('gallery.image.delete')->middleware('can:edit-galleries');

    // Contact Messages (Admin) - use top-level controller like Gallery
    Route::get('/contact-messages', [ContactMessageController::class, 'index'])
        ->name('contact-messages')->middleware('can:browse-contact-messages');

    Route::get('/contact-messages/{id}', [ContactMessageController::class, 'show'])
        ->name('contact-message.show')->middleware('can:read-contact-messages');

    Route::post('/contact-messages/{id}/mark-contacted', [ContactMessageController::class, 'markContacted'])
        ->name('contact-message.mark-contacted')->middleware('can:edit-contact-messages');

    Route::delete('/contact-messages/{id}/delete', [ContactMessageController::class, 'destroy'])
        ->name('contact-message.delete')->middleware('can:delete-contact-messages');

    Route::post('/products/{product}/media/{uuid}/ai-edit', [AiImageController::class, 'edit'])
        ->name('media.ai-edit');
});
