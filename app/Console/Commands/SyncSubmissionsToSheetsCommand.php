<?php

namespace App\Console\Commands;

use App\Jobs\SyncSubmissionToGoogleSheetJob;
use App\Models\Submission;
use Illuminate\Console\Command;

class SyncSubmissionsToSheetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submissions:sync-sheets {--form= : ID atau slug form tertentu} {--limit=50 : Batas jumlah antrean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan sinkronisasi data submission yang belum tersinkron ke Google Sheets';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Memulai sinkronisasi antrean submission ke Google Sheets...");

        $query = Submission::where('sheet_synced', false)
            ->whereHas('form', fn($q) => $q->whereNotNull('target_spreadsheet_id'));

        if ($formFilter = $this->option('form')) {
            $query->whereHas('form', function ($q) use ($formFilter) {
                $q->where('id', $formFilter)->orWhere('slug', $formFilter);
            });
        }

        $limit = (int)$this->option('limit');
        $submissions = $query->limit($limit)->get();

        if ($submissions->isEmpty()) {
            $this->info("Semua submission telah tersinkronisasi ke Google Sheets.");
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$submissions->count()} submission yang tertunda...");

        $bar = $this->output->createProgressBar($submissions->count());
        $bar->start();

        foreach ($submissions as $sub) {
            SyncSubmissionToGoogleSheetJob::dispatchSync($sub);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Proses sinkronisasi submission selesai.");

        return Command::SUCCESS;
    }
}

