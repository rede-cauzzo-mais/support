<?php

namespace RedeCauzzoMais\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RedeCauzzoMais\Api\Telegram;

class TelegramJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct( private readonly string $message, private readonly string|int|null $chatId = null )
    {
    }

    public function handle(): void
    {
        Telegram::sendMessage( $this->message, $this->chatId );
    }
}
