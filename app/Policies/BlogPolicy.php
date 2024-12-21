<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BlogPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Blog $blog): bool
    {
        return self::ownsBlog($user, $blog);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Blog $blog): bool
    {
        return self::ownsBlog($user, $blog);
    }

    public function schedule(User $user, Blog $blog): bool
    {
        return self::ownsBlog($user, $blog);
    }

    private function ownsBlog(User $user, Blog $blog): bool
    {
        return $blog->user_id === $user->id;
    }
}
