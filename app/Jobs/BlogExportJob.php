<?php

namespace App\Jobs;

use App\Exports\BlogsExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class BlogExportJob implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new job instance.
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $filePath = 'Blog_Exports/blogs_' . now()->subDays(7)->format('Y_m_d') . '_to_' . now()->format('Y_m_d') . '.xlsx';

        Excel::store(new BlogsExport(), $filePath);
    }
}
