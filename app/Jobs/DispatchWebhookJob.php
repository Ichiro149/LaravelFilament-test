<?php

namespace App\Jobs;

use App\Models\Webhook;
use App\Services\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 1;

    /**
     * Create a new job instance.
     *
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public Webhook $webhook,
        public string $event,
        public array $payload,
        public int $attempt = 1
    ) {}

    /**
     * Execute the job.
     */
    public function handle(WebhookService $service): void
    {
        $log = $service->send($this->webhook, $this->event, $this->payload, $this->attempt);

        // If failed and can retry, dispatch another job with delay
        if ($log->canRetry()) {
            $delay = $this->calculateBackoff($this->attempt);

            self::dispatch(
                $this->webhook,
                $this->event,
                $this->payload,
                $this->attempt + 1
            )->delay($delay);
        }
    }

    /**
     * Calculate exponential backoff delay.
     */
    protected function calculateBackoff(int $attempt): int
    {
        // Exponential backoff: 30s, 60s, 120s, etc.
        return min(30 * pow(2, $attempt - 1), 3600);
    }
}
