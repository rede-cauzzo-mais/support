<?php

namespace RedeCauzzoMais;

use RedeCauzzoMais\Api\BoletoSicredi;
use Illuminate\Support\ServiceProvider;

class CauzzoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = realpath( realpath( __DIR__ . '/../config/cauzzo.php' ) );

        $this->publishes( [$source => config_path( 'cauzzo.php' )], 'cauzzo' );

        $this->mergeConfigFrom( $source, 'cauzzo' );
    }

    public function register(): void
    {
        $this->app->bind( BoletoSicredi::class, fn() => new BoletoSicredi( config( 'cauzzo.sicredi.boleto' ) ) );

        //$this->app->alias( \App\Facades\ApiBoletoSicredi::class, 'ApiBoletoSicredi' );
    }

    public function provides(): array
    {
        return [BoletoSicredi::class];
    }
}
