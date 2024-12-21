<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\BlogPublished;
use App\Notifications\NewBlogNotification;

class SendBlogPublishedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BlogPublished $event): void
    {
        $blog = $event->blog;
        $author = $blog->user;

        User::where('id', '!=', $author->id)
            ->chunk(100, function ($users) use ($blog, $author) {
                foreach ($users as $user) {
                    $user->notify(new NewBlogNotification($blog, $author));
                }
            });
    }
}
