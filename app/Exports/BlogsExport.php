<?php

namespace App\Exports;

use App\Models\Blog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BlogsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $startDate, $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? now()->subDays(7);
        $this->endDate = $endDate ?? now();
    }
    public function headings(): array
    {
        return [
            'blog id',
            'title',
            'content',
            'likes count',
            'tags',
            'author id',
            'author name',
            'author email',
            'created_at',
            'updated_at'
        ];
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $blogs = Blog::with('tag:name')->with('user')->withCount('like')
            ->where('created_at', '>', $this->startDate)
            ->where('created_at', '<', $this->endDate)
            ->get();

        return $blogs->map(function ($blog) {
            return [
                'blog id' => $blog->id,
                'title' => $blog->title,
                'content' => $blog->content,
                'likes count' => ($blog->like_count == 0) ? "0" : $blog->like_count,
                'tags' => $blog->tag()->pluck('name'),
                'author id' => $blog->user->id,
                'author name' => $blog->user->name,
                'author email' => $blog->user->email,
                'created_at' => $blog->created_at,
                'updated_at' => $blog->updated_at
            ];
        });
    }
}
