<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Blog;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use App\Http\Requests\BlogRequest;
use App\Jobs\BlogFuturePublishJob;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SearchRequest;
use App\Http\Resources\BlogResource;
use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Requests\ScheduleBlogPublishRequest;

class BlogController extends Controller
{
    use ApiResponses;

    public function index()
    {
        return BlogResource::collection(self::readBlogs()
            ->where('is_published', 1)
            ->paginate(5));
    }

    public function indexAsAdmin()
    {
        return BlogResource::collection(self::readBlogs()
            ->paginate(5));
    }

    public function getUserBlogs(Request $request)
    {
        return BlogResource::collection(self::readBlogs()
            ->where('user_id', $request->user()->id)
            ->paginate(5));
    }

    private function readBlogs()
    {
        return Blog::with('tag:name')
            ->with('like')
            ->withCount('like')
            ->with('user')
            ->with([
                'comment' => function ($query) {
                    $query
                        ->with('user')
                        ->with('like')
                        ->withCount('like')
                        ->limit(3)
                        ->latest();
                }
            ])
            ->latest();
    }

    public function store(BlogRequest $blogRequest)
    {
        $blog = Blog::create([
            'title' => $blogRequest->title,
            'content' => $blogRequest->content,
            'user_id' => $blogRequest->user()->id
        ]);

        $tags = TagController::storeTagsAndReturnTagIdsCollection($blogRequest->tags);
        $blog->tag()->sync($tags);

        return $this->ok('Blog created.');
    }

    public function destroy(Blog $blog, Request $request)
    {
        Gate::authorize('delete', $blog);

        $blog->like()->delete();
        $blog->tag()->detach();
        $blog->delete();

        return $this->ok('blog deleted.');
    }

    public function show(Blog $blog, Request $request)
    {
        return BlogResource::collection(self::readBlogs()
            ->where('id', $blog->id)
            ->with([
                'comment' => function ($query) {
                    $query
                        ->with('user')
                        ->with('like')
                        ->withCount('like')
                        ->latest()
                        ->paginate(3);
                }
            ])
            ->get());
    }

    public function update(Blog $blog, BlogRequest $blogRequest)
    {
        Gate::authorize('update', $blog);

        Blog::where('id', $blog->id)->update([
            'title' => $blogRequest->title,
            'content' => $blogRequest->content,
            'user_id' => $blogRequest->user()->id,
            'is_published' => 0
        ]);

        $tags = TagController::storeTagsAndReturnTagIdsCollection($blogRequest->tags);
        $blog->tag()->detach();
        $blog->tag()->sync($tags);

        DB::table('jobs')
            ->where('payload', 'LIKE', '%"id\\\";i:' . $blog->id . ';%')
            ->delete();

        return $this->ok('Blog edited successfully');
    }

    public function search(SearchRequest $searchRequest)
    {
        if ($searchRequest->between == 'all') {
            return BlogResource::collection(
                self::readBlogs()
                    ->whereLike('title', "%{$searchRequest->value}%")
                    ->orWhereLike('content', "%{$searchRequest->value}%")
                    ->orWhereHas('user', function ($query) use ($searchRequest) {
                        $query->whereLike('name', "%{$searchRequest->value}%");
                    })
                    ->paginate(5)
            );
        } else {
            if ($searchRequest->between == 'name') {
                return BlogResource::collection(
                    self::readBlogs()
                        ->whereHas('user', function ($query) use ($searchRequest) {
                            $query->whereLike('name', "%{$searchRequest->value}%");
                        })
                        ->paginate(5)
                );
            }

            return BlogResource::collection(
                self::readBlogs()
                    ->whereLike($searchRequest->between, "%{$searchRequest->value}%")
                    ->paginate(5)
            );
        }
    }

    public function schedule(Blog $blog, ScheduleBlogPublishRequest $scheduleBlogPublishRequest)
    {
        Gate::authorize('schedule', $blog);

        if ($blog->is_published == 1) {
            return $this->error('The blog already published.', 422);
        }

        if (
            DB::table('jobs')
                ->where('payload', 'LIKE', '%"id\\\";i:' . $blog->id . ';%')
                ->exists()
        ) {
            return $this->error('The blog is already scheduled.', 422);
        }

        $executed = RateLimiter::attempt(
            'schedule-blog:' . $scheduleBlogPublishRequest->user()->id,
            5,
            function () {
                return true;
            },
            24 * 60 * 60
        );

        if (!$executed) {
            return $this->error('More than 5 schedule in last 24 hours.', 429);
        }

        BlogFuturePublishJob::dispatch($blog)
            ->delay(Carbon::parse($scheduleBlogPublishRequest->publish_at) ?? now());

        return $this->ok('Blog scheduled successfully.');
    }
}
