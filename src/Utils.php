<?php

namespace RedeCauzzoMais\Support;

use DateTime;
use Exception;
use Illuminate\Support\Collection;

class Utils
{
    public static function assetCache( $asset ): string
    {
        return asset( $asset ) . '?' . md5_file( public_path( $asset ) );
    }

    public static function mask( $str, $mask )
    {
        $str = str_replace( ' ', '', $str );

        for ( $i = 0; $i < strlen( $str ); $i++ ) {
            $mask[strpos( $mask, '#' )] = $str[$i];
        }

        return $mask;
    }

    public static function abreviaNome( ?string $nome, int $limite = 1, bool $truncate = false ): ?string
    {
        if ( is_null( $nome ) ) {
            return null;
        }

        $nome = trim( preg_replace( '/\s+/', ' ', $nome ) );

        if ( $limite >= mb_strlen( $nome, 'utf8' ) ) {
            return $nome;
        }

        $preposicao = ['DA', 'DAS', 'DE', 'DO', 'DOS', 'E', 'Y'];

        $implodeNome = function ( $nome ) {
            return trim( preg_replace( '/\s+/', ' ', implode( ' ', $nome ) ) );
        };

        $nome = explode( ' ', $nome );

        for ( $i = 1; $i < count( $nome ) - 1; $i++ ) {

            if ( in_array( $nome[$i], $preposicao ) ) {

                if ( !isset( $nome[$i + 2] ) ) {
                    break;
                }

                if ( mb_strlen( $implodeNome( $nome ), 'utf8' ) > $limite ) {
                    $nome[$i]     = '';
                    $nome[$i + 1] = mb_substr( $nome[$i + 1], 0, 1, 'utf-8' ) . ".";
                }
                continue;
            }

            if ( mb_strlen( $implodeNome( $nome ), 'utf8' ) > $limite ) {
                $nome[$i] = mb_substr( $nome[$i], 0, 1, 'utf-8' ) . ".";
            }
        }

        $nome = $implodeNome( $nome );

        if ( $truncate ) {
            return mb_substr( $nome, 0, $limite, 'utf8' );
        }

        return $nome;
    }

    public static function dateToFormat( $date, $from, $to ): ?string
    {
        if ( is_null( $date ) ) {
            return null;
        }

        $d = DateTime::createFromFormat( $from, $date );

        if ( $d->format( $from ) <> $date ) {
            return null;
        }

        return $d->format( $to );
    }

    public static function ifAttr( $value1, $value2, $attr )
    {
        if ( !isset( $value1 ) or !isset( $value2 ) ) {
            return '';
        }

        return $value1 == $value2 ? $attr : '';
    }

    public static function option( $list, $key, $value, $ifSelected = null ): string
    {
        $option = '';

        if ( $list instanceof Collection ) {
            $list = $list->toArray();
        }

        foreach ( $list as $row ) {
            $row = (array) $row;

            $selected = static::ifAttr( $ifSelected, $row[$key], 'selected' );

            $option .= "<option value=\"{$row[$key]}\" {$selected}>{$row[$value]}</option>";
        }

        return $option;
    }

    /**
     * @param string|null $type
     * @param             $var
     *
     * @return false|float|int|mixed|string
     * @throws \Exception
     */
    public static function cast( ?string $type, $var ): mixed
    {
        if ( is_null( $type ) ) {
            return $var;
        }

        switch ( $type ) {
            case 'int':
                $var = (int) $var;
                break;
            case 'float':
                $var = (float) $var;
                break;
            case 'string':
                $var = (string) $var;
                break;
            case 'array':
                if ( is_array( $var ) ) {
                    $var = json_encode( $var, JSON_UNESCAPED_UNICODE );
                } elseif ( is_string( $var ) ) {
                    $var = json_decode( $var, true );
                }

                if ( json_last_error() == 4 ) {
                    throw new Exception( json_last_error_msg() );
                }
        }

        return $var;
    }

    public static function money( $valor, $cifrao = true ): string
    {
        if ( $cifrao ) {
            return 'R$ ' . number_format( $valor, 2, ',', '.' );
        }

        return number_format( $valor, 2, ',', '.' );
    }

    private static function substr( $string, $start, $length ): string
    {
        return mb_substr( $string, $start, $length, 'utf8' );
    }

    public static function getMesBr( ?int $num_mes = null ): array|string
    {
        $mes = [
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

        if ( !is_null( $num_mes ) ) {

            return $mes[$num_mes];
        }

        return $mes;
    }

    public static function getHoje( $hoje = null ): string
    {
        if ( is_null( $hoje ) ) {
            [$ano, $mes, $dia] = explode( '-', date( 'Y-n-d' ) );
        } else {
            [$ano, $mes, $dia] = explode( '-', substr( $hoje, 0, 10 ) );
        }

        return $dia . ' de ' . self::getMesBr( $mes ) . ' de ' . $ano;
    }

    public static function toThousand( $valor, $decimals = 0 ): string
    {
        return number_format( $valor, $decimals, ',', '.' );
    }
}
