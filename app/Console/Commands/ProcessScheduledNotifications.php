<?php

namespace App\Console\Commands;

use App\Models\ScheduledNotification;
use Illuminate\Console\Command;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process-scheduled
                            {--dry-run : Show what would be processed without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled notifications that are due to be sent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN MODE - No notifications will actually be sent');
        }

        $this->info('Processing scheduled notifications...');

        $pendingNotifications = ScheduledNotification::pending()->get();

        if ($pendingNotifications->isEmpty()) {
            $this->info('No scheduled notifications to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingNotifications->count()} scheduled notifications to process");

        $bar = $this->output->createProgressBar($pendingNotifications->count());
        $bar->start();

        $sent = 0;
        $failed = 0;

        foreach ($pendingNotifications as $scheduled) {
            try {
                if (!$dryRun) {
                    $success = $scheduled->send();
                    if ($success) {
                        $sent++;
                        $this->line(" ✓ Sent: {$scheduled->title}");
                    } else {
                        $failed++;
                        $this->line(" ✗ Failed: {$scheduled->title}");
                    }
                } else {
                    $this->line(" [DRY RUN] Would send: {$scheduled->title}");
                    $sent++;
                }
            } catch (\Exception $e) {
                $failed++;
                $this->error("Error processing notification {$scheduled->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("Processing complete:");
        $this->line("  Sent: {$sent}");
        $this->line("  Failed: {$failed}");

        return Command::SUCCESS;
    }
}
