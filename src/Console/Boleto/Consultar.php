<?php

namespace RedeCauzzoMais\Console\Boleto;

use Illuminate\Console\Command;
use RedeCauzzoMais\Facades\ApiBoletoSicredi;
use Throwable;

class Consultar extends Command
{
    protected $signature   = 'boleto:consultar {idBoleto}';
    protected $description = 'Consulta boleto pelo nosso número';

    public function handle(): int
    {
        try {
            $nossoNumero = preg_replace( "/\D/", '', $this->argument( 'idBoleto' ) );

            if ( strlen( $nossoNumero ) <> 9 ) {
                throw new \Exception( 'Nosso número precisa ter 9 digitos' );
            }

            $boleto = ApiBoletoSicredi::consulta( $nossoNumero );
            $this->info( print_r( $boleto, true ) );

            return self::SUCCESS;
        } catch ( Throwable $e ) {
            $this->error( $e->getMessage() );

            return self::FAILURE;
        }
    }
}
