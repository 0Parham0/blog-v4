<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Comment;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    use ApiResponses;

    public function store(Blog $blog, Request $request, ?Comment $comment = null)
    {
        if ($comment && $comment->blog_id != $blog->id) {
            return $this->error('The comment is not for this blog.', 404);
        }

        $likeable = $comment ?? $blog;
        if ($likeable->like()->where('user_id', $request->user()->id)->exists()) {
            return $this->error('Already liked.', 422);
        }

        $likeable->like()->create([
            'user_id' => $request->user()->id
        ]);

        return $this->ok('Liked');
    }

    public function destroy(Blog $blog, Request $request, ?Comment $comment = null)
    {
        if ($comment && $comment->blog_id != $blog->id) {
            return $this->error('The comment is not for this blog.', 404);
        }

        $likeable = $comment ?? $blog;
        if (!$likeable->like()->where('user_id', $request->user()->id)->exists()) {
            return $this->error('Did not liked.', 422);
        }

        $likeable->like()->where('user_id', $request->user()->id)
            ->delete();

        return $this->ok('Unliked');
    }
}
