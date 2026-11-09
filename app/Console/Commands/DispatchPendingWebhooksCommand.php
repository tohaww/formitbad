<?php

namespace App\Console\Commands;

use App\Jobs\DispatchWebhookJob;
use App\Models\Submission;
use Illuminate\Console\Command;

class DispatchPendingWebhooksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submissions:dispatch-webhooks {--form= : ID atau slug form tertentu} {--limit=50 : Batas jumlah antrean}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menjalankan pengiriman webhook bagi submission yang belum terkirim';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Memulai pengiriman antrean webhook...");

        $query = Submission::where('webhook_delivered', false)
            ->whereHas('form', fn($q) => $q->whereNotNull('webhook_url')->where('webhook_url', '!=', ''));

        if ($formFilter = $this->option('form')) {
            $query->whereHas('form', function ($q) use ($formFilter) {
                $q->where('id', $formFilter)->orWhere('slug', $formFilter);
            });
        }

        $limit = (int)$this->option('limit');
        $submissions = $query->limit($limit)->get();

        if ($submissions->isEmpty()) {
            $this->info("Semua webhook telah berhasil terkirim.");
            return Command::SUCCESS;
        }

        $this->info("Ditemukan {$submissions->count()} pengiriman webhook tertunda...");

        $bar = $this->output->createProgressBar($submissions->count());
        $bar->start();

        foreach ($submissions as $sub) {
            DispatchWebhookJob::dispatchSync($sub);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Proses pengiriman antrean webhook selesai.");

        return Command::SUCCESS;
    }
}

