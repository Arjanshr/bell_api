<?php

namespace App\Providers;

use App\Models\Order;
use App\Models\QuestionsAndAnswer;
use App\Observers\OrderObserver;
use App\Observers\QuestionsAndAnswerObserver;
use App\Observers\NotificationObserver;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Pagination\Paginator;
use JeroenNoten\LaravelAdminLte\Events\BuildingMenu;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\OptionalAuthenticate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(Dispatcher $events): void
    {
        // Register model observers for notifications
        Order::observe(OrderObserver::class);
        QuestionsAndAnswer::observe(QuestionsAndAnswerObserver::class);
        \App\Models\Notification::observe(NotificationObserver::class);

        $events->listen(BuildingMenu::class, function (BuildingMenu $event) {
            $count_orders = Order::where('status', '=', 'pending')->count();
            $event->menu->addAfter('search', [
                'can' => 'browse-orders',
                'key' => 'manage_orders',
                'text' => 'Manage Orders',
                'url' => 'admin/orders',
                'label' => $count_orders,
                'label_color' => 'success',
                'icon'    => 'fas fa-shopping-cart',
            ]);

            // Add SUPPORT section header
            $event->menu->addAfter('manage_orders', [
                'header' => 'SUPPORT',
                'key' => 'support',
            ]);
            // Add Questions menu item with unanswered count
            $count_questions = \App\Models\QuestionsAndAnswer::where('status', 'unanswered')->count();
            $event->menu->addAfter('support', [
                'key' => 'questions',
                'text' => 'Questions',
                'url' => 'admin/questions',
                'icon' => 'fas fa-question-circle',
                'label' => $count_questions,
                'label_color' => 'warning',
                'active' => ['questions', 'questions*'],
            ]);

            // Add Contact Messages menu item with unread count
            $count_unread_messages = \App\Models\ContactMessage::where('status', 'unread')->count();
            $event->menu->addAfter('questions', [
                'key' => 'contact_messages',
                'text' => 'Contact Messages',
                'url' => 'admin/contact-messages',
                'icon' => 'far fa-envelope',
                'label' => $count_unread_messages,
                'label_color' => 'danger',
                'active' => ['contact-messages', 'contact-messages*'],
            ]);
        });
        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
        Paginator::useBootstrap();
        Route::aliasMiddleware('optional.auth', OptionalAuthenticate::class);
    }
}
