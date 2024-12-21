<?php

namespace App\Http\Controllers;

use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use App\Exports\BlogsExport;
use App\Traits\ApiResponses;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class BlogExportController extends Controller
{
    use ApiResponses;

    public function index()
    {
        $files = Storage::disk('local')->files('Blog_Exports');
        return array_map(function ($file) {
            return Str::after($file, 'Blog_Exports/');
        }, $files);
    }

    public function download($fileName)
    {
        $fullPath = 'Blog_Exports/' . $fileName;

        if (Storage::disk('local')->exists($fullPath)) {
            return Storage::disk('local')->download($fullPath);
        }

        return $this->error('File not found.', 404);
    }

    public function manualExport($startDate, $endDate)
    {
        if ($startDate == null || $endDate == null) {
            Excel::store(new BlogsExport($startDate, $endDate), 'Blog_Exports/last_week_blogs_' . now()->format('Y_m_d_H_i_s') . '.xlsx');
        } else {
            $period = CarbonPeriod::create($startDate, '7 days', $endDate);

            foreach ($period as $date) {
                $periodStart = $date;
                $periodEnd = $date->copy()->addDays(7);

                if ($periodEnd->gt($endDate)) {
                    $periodEnd = $endDate;
                }

                $fileName = 'Blog_Exports/blogs_' . $periodStart->format('Y_m_d') . '_to_' . $periodEnd->format('Y_m_d') . '.xlsx';
                Excel::store(new BlogsExport($periodStart, $periodEnd), $fileName);
            }
        }
    }
}
