<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    public function toggleStatus($id)
    {
        $review = Review::findOrFail($id);
        $review->status = $review->status === 'confirmed' ? 'pending' : 'confirmed';
        $review->save();

        return back()->with('success', 'Review status updated.');
    }
}
