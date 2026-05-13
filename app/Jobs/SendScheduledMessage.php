<?php

namespace App\Jobs;

use App\Models\Message;
use App\Services\MessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Message $message;

    /**
     * Create a new job instance.
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(MessageService $messageService): void
    {
        try {
            // Check if message is still scheduled and not sent
            if (! $this->message->scheduled_at ||
                $this->message->sent_at ||
                now()->isBefore($this->message->scheduled_at)) {
                return;
            }

            // Send the message
            $success = $this->message->send();

            if ($success) {
                Log::info("Scheduled message {$this->message->id} sent successfully");
            } else {
                Log::error("Failed to send scheduled message {$this->message->id}");
            }

        } catch (\Exception $e) {
            Log::error("Error sending scheduled message {$this->message->id}: {$e->getMessage()}");

            // Could implement retry logic here
            if ($this->attempts() < 3) {
                $this->release(60); // Retry after 1 minute
            }
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Scheduled message job failed for message {$this->message->id}: {$exception->getMessage()}");
    }
}
