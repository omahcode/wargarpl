<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhookJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $event;
    protected $data;

    /**
     * Create a new job instance.
     */
    public function __construct(string $url, string $event, array $data)
    {
        $this->url = $url;
        $this->event = $event;
        $this->data = $data;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $payload = [
                'event' => $this->event,
                'timestamp' => now()->toIso8601String(),
                'data' => $this->data,
            ];

            $response = Http::timeout(5)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'BankDataWebhook/1.0',
                ])
                ->post($this->url, $payload);

            if ($response->failed()) {
                Log::warning("Webhook failed to dispatch to {$this->url} with status code {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("Webhook error dispatching to {$this->url}: " . $e->getMessage());
        }
    }
}
