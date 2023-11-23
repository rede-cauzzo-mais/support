<?php

namespace RedeCauzzoMais;

use RedeCauzzoMais\Api\BoletoSicredi;
use Illuminate\Support\ServiceProvider;
use RedeCauzzoMais\Console\Boleto\Consultar as BoletoConsultar;

class CauzzoServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = realpath( realpath( __DIR__ . '/../config/cauzzo.php' ) );

        $this->publishes( [$source => config_path( 'cauzzo.php' )], 'cauzzo' );

        $this->mergeConfigFrom( $source, 'cauzzo' );

        if ( $this->app->runningInConsole() ) {
            $this->commands( [
                BoletoConsultar::class,
            ] );
        }
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
