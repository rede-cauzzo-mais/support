<?php

namespace RedeCauzzoMais\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use RedeCauzzoMais\Api\Trello;
use Throwable;

class TrelloReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct( private readonly Throwable $e )
    {
    }

    public function handle(): void
    {
        Trello::report( $this->e );
    }
}
