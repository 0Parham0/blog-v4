<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'comment_id' => $this->id,
            'author' => new UserResource($this->user),
            'body' => $this->body,
            'like_count' => $this->like_count,
            'created_at' => $this->created_at,
        ];

        $user = $request->user();
        if ($user) {
            $response = array_merge($response, [
                'is_liked' => $this->like()->where('user_id', $user->id)->exists(),
            ]);
        }

        return $response;
    }
}
