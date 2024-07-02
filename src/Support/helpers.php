<?php

use RedeCauzzoMais\Support\Cronos;
use RedeCauzzoMais\Support\Money;
use RedeCauzzoMais\Support\Utils;

if ( !function_exists( 'assetCache' ) ) {
    function assetCache( $asset ): string
    {
        return Utils::assetCache( $asset );
    }
}

if ( !function_exists( 'userConfig' ) ) {
    function userConfig( $key, $default = null )
    {
        return session( 'config' )[$key] ?? $default;
    }
}

if ( !function_exists( 'exception' ) ) {
    /**
     * @param $message
     *
     * @throws Exception
     */
    function exception( $message )
    {
        throw new Exception( $message );
    }
}

if ( !function_exists( 'rDate' ) ) {
    function rDate( $data )
    {
        return Cronos::rDate( $data );
    }
}

if ( !function_exists( 'age' ) ) {
    function age( $birthday, $today = null ): int|string
    {
        return Cronos::age( $birthday, $today );
    }
}

if ( !function_exists( 'getFullAge' ) ) {
    function getFullAge( $age )
    {
        return Cronos::getFullAge( $age );
    }
}

if ( !function_exists( 'sDateTime' ) ) {
    function sDateTime( $date )
    {
        return Cronos::sDateTime( $date );
    }
}

if ( !function_exists( 'sDate' ) ) {
    function sDate( $date )
    {
        return Cronos::sDate( $date );
    }
}

if ( !function_exists( 'sTime' ) ) {
    function sTime( $dateOrTime, $format = 'H:i' ): string
    {
        return Cronos::sTime( $dateOrTime, $format );
    }
}

if ( !function_exists( 'brDayOfWeek' ) ) {
    function brDayOfWeek( $date ): array|string
    {
        return Cronos::brDayOfWeek( $date );
    }
}

if ( !function_exists( 'brToday' ) ) {
    function brToday( $today = null ): string
    {
        return Cronos::brToday( $today );
    }
}

if ( !function_exists( 'brMonth' ) ) {
    function brMonth( $numMonth = null ): array|string
    {
        return Cronos::brMonth( $numMonth );
    }
}

if ( !function_exists( 'timeElapsedSinceNow' ) ) {
    function timeElapsedSinceNow( $datetime, $full = false ): string
    {
        return Cronos::timeElapsedSinceNow( $datetime, $full );
    }
}

if ( !function_exists( 'money' ) ) {
    function money( $valor, $html = false ): string
    {
        return Money::format( $valor, $html );
    }
}

if ( !function_exists( 'toPercent' ) ) {
    function toPercent( $total, $base, $precision = 2, $html = true ): string
    {
        return Money::toPercent( $total, $base, $precision, $html );
    }
}

if ( !function_exists( 'formatPercent' ) ) {
    function formatPercent( float $percent, $precision = 2 ): string
    {
        return Money::formatPercent( $percent, $precision );
    }
}

if ( !function_exists( 'toGrowthPercent' ) ) {
    function toGrowthPercent( $new, $old, $precision = 2, $html = true ): string
    {
        return Money::toGrowthPercent( $new, $old, $precision, $html );
    }
}

if ( !function_exists( 'toThousand' ) ) {
    function toThousand( $valor, $decimals = 0 ): string
    {
        return Utils::toThousand( $valor, $decimals );
    }
}

if ( !function_exists( 'abreviaNome' ) ) {
    function abreviaNome( ?string $nome, int $limite = 1, bool $truncate = false ): ?string
    {
        return Utils::abreviaNome( $nome, $limite, $truncate );
    }
}

if ( !function_exists( 'option' ) ) {
    function option( $list, $key, $value, $selected = null ): string
    {
        return Utils::option( $list, $key, $value, $selected );
    }
}
