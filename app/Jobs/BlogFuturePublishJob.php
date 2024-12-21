<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Events\BlogPublished;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class BlogFuturePublishJob implements ShouldQueue, ShouldBeUnique
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(protected Blog $blog)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->blog->update(['is_published' => 1]);
        event(new BlogPublished($this->blog));
    }

    public function uniqueId(): string
    {
        return $this->blog->id;
    }
}
