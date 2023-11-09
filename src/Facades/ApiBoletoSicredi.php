<?php

namespace RedeCauzzoMais\Facades;

use RedeCauzzoMais\Api\BoletoSicredi;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string getAccessToken()
 * @method static false|array consulta( int $nossoNumero )
 * @method static false|array pedidoBaixa( int $nossoNumero )
 * @method static false|array liquidados( \Illuminate\Support\Carbon $dia, ?string $cpf = null, ?int $pagina = 1 )
 * @method static false|array novoVencimento( int $nossoNumero, \Illuminate\Support\Carbon $dia )
 * @method static false|array registro( array $boleto )
 */
class ApiBoletoSicredi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return BoletoSicredi::class;
    }
}
