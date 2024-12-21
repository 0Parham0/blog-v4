<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Blog;
use App\Models\Comment;
use App\Traits\ApiResponses;

class CommentController extends Controller
{
    use ApiResponses;

    public function store(Blog $blog, CommentRequest $commentRequest)
    {
        Comment::create([
            'blog_id' => $blog->id,
            'user_id' => $commentRequest->user()->id,
            'body' => $commentRequest->body
        ]);

        return $this->ok('Comment added.');
    }
}
