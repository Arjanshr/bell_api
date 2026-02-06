<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class QuestionsController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->input('filter');
        $status = $request->input('status');
        $category = $request->input('category');

        $questions = \App\Models\QuestionsAndAnswer::query()
            ->when($filter, function ($query) use ($filter) {
                $query->where('question', 'like', '%'.$filter.'%');
            })
            ->when($status, function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->when($category, function ($query) use ($category) {
                $query->whereHas('product', function ($q) use ($category) {
                    $q->whereHas('categories', function ($qc) use ($category) {
                        $qc->where('id', $category);
                    });
                });
            })
            ->orderByDesc('created_at')
            ->get();

        $categories = \App\Models\Category::orderBy('name')->get();

        return view('admin.questions.index', compact('questions', 'categories', 'status', 'category', 'filter'));
    }

    public function answer($id)
    {
        $question = \App\Models\QuestionsAndAnswer::findOrFail($id);
        return view('admin.questions.answer', compact('question'));
    }

    public function submitAnswer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string',
        ]);
        $question = \App\Models\QuestionsAndAnswer::findOrFail($id);
        $question->answer = $request->answer;
        $question->status = 'answered';
        $question->save();
        return redirect()->route('questions')->with('success', 'Answer submitted successfully.');
    }
    public function delete($id)
    {
        $question = \App\Models\QuestionsAndAnswer::findOrFail($id);
        $question->delete();
        return redirect()->route('questions')->with('success', 'Question deleted successfully.');
    }
}
