<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Http\Controllers\BlogExportController;

class ManualBlogsExport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'blogs:export
        {--start= : Start date (format: Y-m-d)}
        {--end= : End date (format: Y-m-d)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export blogs within a date range';

    protected $blogExportController;

    public function __construct(BlogExportController $blogExportController){
        parent::__construct();
        $this->blogExportController = $blogExportController;
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $startDate = $this->option('start');
            $endDate = $this->option('end');

            if ($startDate) {
                $startDate = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();
            } else {
                $startDate = now()->subDays(7)->startOfDay();
                $this->info("Using default start date: " . $startDate->format('Y-m-d'));
            }

            if ($endDate) {
                $endDate = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay();
            } else {
                $endDate = now()->endOfDay();
                $this->info("Using default end date: " . $endDate->format('Y-m-d'));
            }

            if ($startDate->gt($endDate)) {
                $this->error("Start date cannot be after end date");
                return 1;
            }

            $this->info("Exporting blogs from {$startDate->format('Y-m-d')} to {$endDate->format('Y-m-d')}");

            $this->blogExportController->manualExport($startDate, $endDate);

            $this->info("Export completed!");

            return 0;
        } catch (\Exception $e) {
            $this->error("An error occurred: " . $e->getMessage());
            return 1;
        }
    }
}
