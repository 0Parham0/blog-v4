<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $response = [
            'blog_id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
            'like_count' => $this->like_count,
            'tags' => TagResource::collection($this->tag)->pluck('name'),
            'author' => new UserResource($this->user),
            'comments' => CommentResource::collection($this->comment),
        ];

        $user = $request->user();
        if ($user) {
            $response = array_merge($response, [
                'is_liked' => $this->like()->where('user_id', $user->id)->exists(),
                'is_published' => $this->is_published,
            ]);
        }

        return $response;
    }
}
