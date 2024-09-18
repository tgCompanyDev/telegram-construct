<?php

namespace Valibool\TelegramConstruct\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Valibool\TelegramConstruct\Services\Messages\Output\OutputMessage;

class DeleteTGMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private int $messageId;
    private string $botToken;
    private string $chatId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $botToken, int $messageId, string $chatId)
    {
        $this->messageId = $messageId;
        $this->botToken = $botToken;
        $this->chatId = $chatId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $delete = new OutputMessage($this->botToken);
        $delete->deleteMessage($this->chatId,$this->messageId);
    }
}
