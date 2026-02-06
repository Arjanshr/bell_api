<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class QuestionsAndAnswersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        $is_owner = $user && $user->id === $this->user_id;
        $is_unanswered = empty($this->answer);

        return [
            "id" => $this->id,
            "user" => $this->user->only(['id', 'name', 'email','profile_photo_url']),
            "question" => $this->question,
            "answer" => $this->answer,
            "can_edit" => $is_owner && $is_unanswered,
            "can_delete" => $is_owner && $is_unanswered,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
