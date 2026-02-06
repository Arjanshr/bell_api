<?php


namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\BaseController;
use App\Models\QuestionsAndAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class QuestionsAndAnswerController extends BaseController
{
    // Post a new question
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'question' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        QuestionsAndAnswer::create([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'question' => $request->question,
            'answer' => '',
            'status' => 'unanswered',
        ]);

        return $this->sendResponse(null, 'Question posted successfully.');
    }

    // Update a question
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'sometimes|exists:products,id',
            'question' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $question = QuestionsAndAnswer::findOrFail($id);
        $question->fill($request->only(['product_id', 'question']))->save();

        return $this->sendResponse(null, 'Question updated successfully.');
    }

    // Delete a question
    public function destroy($id)
    {
        $question = QuestionsAndAnswer::findOrFail($id);
        $question->delete();
        return $this->sendResponse(null, 'Question deleted successfully.');
    }
}
