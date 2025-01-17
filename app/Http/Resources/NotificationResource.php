<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->data['title'],
            'author_name' => $this->data['author_name'],
            'author_email' => $this->data['author_email'],
            'url' => $this->data['url'],
            'read_at' => $this->read_at,
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
