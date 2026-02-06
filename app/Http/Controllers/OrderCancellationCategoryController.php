<?php

namespace App\Http\Controllers;

use App\Models\OrderCancellationCategory;
use Illuminate\Http\Request;

class OrderCancellationCategoryController extends Controller
{
    public function index()
    {
        $categories = OrderCancellationCategory::orderByDesc('id')->get();
        return view('admin.order_cancellation_categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.order_cancellation_categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        $category = new OrderCancellationCategory();
        $category->name = $data['name'];
        $category->status = $data['status'] ?? 1;
        $category->save();

        toastr()->success('Cancellation category created.');
        return redirect()->route('order-cancellation-categories');
    }

    public function edit(OrderCancellationCategory $orderCancellationCategory)
    {
        $category = $orderCancellationCategory;
        return view('admin.order_cancellation_categories.edit', compact('category'));
    }

    public function update(Request $request, OrderCancellationCategory $orderCancellationCategory)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'status' => 'nullable|in:0,1',
        ]);

        $orderCancellationCategory->name = $data['name'];
        $orderCancellationCategory->status = $data['status'] ?? 1;
        $orderCancellationCategory->save();

        toastr()->success('Cancellation category updated.');
        return redirect()->route('order-cancellation-categories');
    }

    public function destroy(OrderCancellationCategory $orderCancellationCategory)
    {
        $orderCancellationCategory->delete();
        toastr()->success('Cancellation category deleted.');
        return redirect()->route('order-cancellation-categories');
    }
}
