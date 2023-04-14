<?php

namespace RedeCauzzoMais\Support;

use Carbon\Carbon;
use DateTime;
use Throwable;

class Cronos
{
    public static function brMonth( $numMonth = null )
    {
        if ( !is_numeric( $numMonth ) and !is_null( $numMonth ) ) {
            $numMonth = date( 'n', strtotime( $numMonth ) );
        }

        $month = [
            1  => 'Janeiro',
            2  => 'Fevereiro',
            3  => 'Março',
            4  => 'Abril',
            5  => 'Maio',
            6  => 'Junho',
            7  => 'Julho',
            8  => 'Agosto',
            9  => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];

        if ( is_null( $numMonth ) ) {
            return $month;
        }

        return $month[(int) $numMonth];
    }

    public static function brDayOfWeek( $numWeek )
    {
        if ( !is_numeric( $numWeek ) ) {
            $numWeek = date( 'w', strtotime( $numWeek ) );
        }

        $week = [
            0 => 'Domingo',
            1 => 'Segunda',
            2 => 'Terça',
            3 => 'Quarta',
            4 => 'Quinta',
            5 => 'Sexta',
            6 => 'Sábado',
        ];

        if ( is_null( $numWeek ) ) {
            return $week;
        }

        return $week[(int) $numWeek];
    }

    public static function brMonthByYear( $months )
    {
        $frequency = [
            1  => 'Anual',
            2  => 'Semestral',
            4  => 'Trimestral',
            6  => 'Bimestral',
            12 => 'Mensal',
        ];

        if ( is_null( $months ) ) {
            return $frequency;
        }

        return $frequency[(int) $months];
    }

    public static function sTime( $dateOrTime, $format = 'H:i' )
    {
        return date( $format, strtotime( $dateOrTime ) );
    }

    public static function brToday( $today = null )
    {
        [ $year, $month, $day ] = explode( '-', $today ? substr( $today, 0, 10 ) : date( 'Y-n-d' ) );

        return $day . ' de ' . self::brMonth( $month ) . ' de ' . $year;
    }

    public static function sDateTime( $date, $default = null )
    {
        if ( empty( $date ) ) {
            return $default;
        }

        return date( 'd/m/y H:i', strtotime( $date ) );
    }

    public static function sDate( $date, $default = null )
    {
        if ( empty( $date ) ) {
            return $default;
        }

        return date( 'd/m/y', strtotime( $date ) );
    }

    public static function timeElapsedSinceNow( $datetime, $full = false )
    {
        try {
            $now  = new DateTime;
            $then = new DateTime( $datetime );
            $diff = (array) $now->diff( $then );

            $diff['w'] = floor( $diff['d'] / 7 );
            $diff['d'] -= $diff['w'] * 7;

            $string = [
                'y' => 'ano',
                'm' => 'mês',
                'w' => 'semana',
                'd' => 'dia',
                'h' => 'hora',
                'i' => 'minuto',
                's' => 'segundo',
            ];

            foreach ( $string as $k => &$v ) {
                if ( $diff[$k] ) {
                    if ( $v == 'mês' ) {
                        $v = $diff[$k] . ' ' . ( $diff[$k] > 1 ? 'meses' : $v );
                        continue;
                    }

                    $v = $diff[$k] . ' ' . $v . ( $diff[$k] > 1 ? 's' : '' );
                } else {
                    unset( $string[$k] );
                }
            }

            if ( !$full ) {
                $string = array_slice( $string, 0, 1 );
            }

            return $string ? 'há ' . implode( ', ', $string ) : 'agora';
        } catch ( Throwable $e ) {
            return '';
        }
    }

    public static function hoursToMinutes( $hours )
    {
        $separatedData = explode( ':', $hours );

        $minutesInHours    = (int) $separatedData[0] * 60;
        $minutesInDecimals = (int) $separatedData[1];

        return $minutesInHours + $minutesInDecimals;
    }

    public static function minutesToHours( $minutes )
    {
        $hours          = floor( $minutes / 60 );
        $decimalMinutes = $minutes - floor( $minutes / 60 ) * 60;

        return sprintf( "%d:%02.0f", $hours, $decimalMinutes );
    }

    public static function getArrayCalendar( $now )
    {
        if ( is_null( static::rDate( $now ) ) ) {
            return [];
        }

        $timestamp = strtotime( $now );

        $calendar['year']  = date( 'Y', $timestamp );
        $calendar['month'] = date( "m", $timestamp );
        $calendar['pos0']  = date( "w", mktime( 0, 0, 0, $calendar['month'], 1, $calendar['year'] ) );

        $calendar['month_br'] = static::brMonth( $calendar['month'] );

        for ( $i = 1; $i <= date( "t", $timestamp ); $i++ ) {
            $date['day']  = $i;
            $date['date'] = "{$calendar['year']}-{$calendar['month']}-" . str_pad( $i, 2, '0', STR_PAD_LEFT );

            $calendar[] = $date;
        }

        return $calendar;
    }

    public static function rDate( $data )
    {
        if ( is_null( $data ) ) {
            return null;
        }

        $formato = $hora = '';

        if ( strlen( $data ) == 19 ) {
            $formato = ' H:i:s';

            [ $data, $hora ] = explode( ' ', $data );

            $hora = ' ' . $hora;
        }

        if ( strstr( $data, '/' ) && strlen( $data ) == 10 ) {
            $formato = 'Y-m-d' . $formato;

            [ $dia, $mes, $ano ] = explode( '/', $data );
            $data = Utils::mask( $ano . $mes . $dia, '####-##-##' ) . $hora;
        } elseif ( strstr( $data, '-' ) && strlen( $data ) == 10 ) {
            $formato = 'd/m/Y' . $formato;

            [ $ano, $mes, $dia ] = explode( '-', $data );
            $data = Utils::mask( $dia . $mes . $ano, '##/##/####' ) . $hora;
        }

        $d = DateTime::createFromFormat( $formato, $data );

        if ( $d and $d->format( $formato ) == $data ) {
            return $data;
        }

        return null;
    }

    public static function age( $birthday, $today = null )
    {
        try {
            if ( is_null( $birthday ) or empty( $birthday ) ) {
                return -1;
            }

            [ $todayYear, $todayMonth, $todayDay ] = explode( '-', $today ?? date( 'Y-m-d' ) );

            [ $year, $month, $day ] = explode( '-', $birthday );

            if ( !checkdate( $month, $day, $year ) ) {
                return -1;
            }

            $idade = $todayYear - $year;

            if ( ( (int) $month . $day ) > ( (int) ( $todayMonth . $todayDay ) ) ) {
                $idade--;
            }

            return $idade;
        } catch ( Throwable $e ) {
            return -1;
        }
    }

    public static function isDate( $date, $format = 'Y-m-d' )
    {
        $d = DateTime::createFromFormat( $format, $date );

        return $d && $d->format( $format ) == $date;
    }

    /**
     * Calcula o intervalo entre duas datas no formato ISO, o intervalo é dado
     * no formato específicado em intevalor q pode ser
     * y - ano
     * m - meses
     * d - dias
     * h - horas
     * i - minutos
     * default segundos
     *
     * @param string $data1
     * @param string $data2
     * @param string $intervalo m, d, h, i, y
     *
     * @return int|string intervalo de horas
     */
    public static function dateDiff( $data1, $data2, $intervalo )
    {
        switch ( $intervalo ) {
            case 'y':
                $q = 86400 * 365;
                break; //ano
            case 'm':
                $q = 2592000;
                break; //mes
            case 'd':
                $q = 86400;
                break; //dia
            case 'h':
                $q = 3600;
                break; //hora
            case 'i':
                $q = 60;
                break; //minuto
            default:
                $q = 1;
                break; //segundo
        }

        return (int) round( ( strtotime( $data2 ) - strtotime( $data1 ) ) / $q );
    }

    public static function geraVencimentoNoDia( $hoje, $dia, $qnt = 12 )
    {
        $pmes = date( 'Y-m-' . $dia, strtotime( $hoje . ' +20 days' ) );

        $diff = strtotime( $pmes ) - strtotime( $hoje );
        $diff = floor( $diff / ( 60 * 60 * 24 ) );

        if ( $diff < 20 ) {
            $pmes = date( 'Y-m-d', strtotime( $pmes . ' +1 month' ) );
        }
        $vencimento[] = $hoje;

        for ( $i = 0; $i < ( $qnt - 1 ); $i++ ) {
            $vencimento[] = date( 'Y-m-d', strtotime( $pmes . " +{$i} month" ) );
        }

        return $vencimento;
    }

    public static function geraVencimentoMes( $hoje, $dia = null, $qnt = 12 )
    {
        $mes = date( 'n', strtotime( $hoje ) );

        $vencimento[] = $hoje;

        if ( is_null( $dia ) ) {
            $dia = date( 'd', strtotime( $hoje ) );
        }
        $dia = str_pad( $dia, 2, '0', STR_PAD_LEFT );

        for ( $i = 1; $i < $qnt; $i++ ) {
            $mes = date( 'n', mktime( 0, 0, 0, $mes + 1, 1, date( 'Y' ) ) );
            $v   = date( 'Y-m-' . $dia, strtotime( "{$hoje} +" . ( $i ) . " month" ) );

            if ( $mes != date( 'n', strtotime( $v ) ) ) {
                while ( true ) {
                    $v = date( 'Y-m-d', strtotime( $v . " -1 day" ) );
                    if ( $mes == date( 'n', strtotime( $v ) ) ) {
                        break;
                    }
                }
            }
            $vencimento[] = $v;
        }

        return $vencimento;
    }

    public static function nextBusinessDay( Carbon $date, array $feriados = [], int $businessDay = 0 ): string
    {
        for ( $i = 0; $i < 50; $i++ ) {
            if ( in_array( $date->englishDayOfWeek, ['Saturday', 'Sunday'] ) ) {
                $date->addDay();

                continue;
            }

            if ( in_array( $date->format( 'Y-m-d' ), $feriados ) ) {
                $date->addDay();

                continue;
            }

            if ( $businessDay > 0 ) {
                $businessDay--;

                $date->addDay();

                continue;
            }
        }

        return $date->format( 'Y-m-d' );
    }
}
