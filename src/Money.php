<?php

namespace RedeCauzzoMais\Support;

use Throwable;

class Money
{
    public static function format( $valor, $html = false ): string
    {
        if ( $valor < 0 ) {
            $valor *= -1;

            if ( $html ) {
                return '<span class="text-danger">-R$ ' . number_format( $valor, 2, ',', '.' ) . '</span>';
            }

            return '-R$ ' . number_format( $valor, 2, ',', '.' );
        }

        return 'R$ ' . number_format( $valor, 2, ',', '.' );
    }

    public static function unformat( $valor ): string
    {
        $valorUnformated = preg_replace( '/[^0-9.,-]/', '', $valor );

        if ( preg_match( "/^(-?\d{1,3})?(.\d{3})*(,\d{1,2})?$/", $valorUnformated ) ) {
            $valorUnformated = str_replace( '.', '', $valorUnformated );
            $valorUnformated = str_replace( ',', '.', $valorUnformated );
        } elseif ( preg_match( "/^(-?\d{1,3})?(,\d{3})*(\.\d{1,2})?$/", $valorUnformated ) ) {
            $valorUnformated = str_replace( ',', '', $valorUnformated );
        }

        return number_format( $valorUnformated, 2, '.', '' );
    }

    public static function toPercent( $total, $base, $precision = 2, $html = true ): string
    {
        try {
            $percent = $base / $total * 100;
        } catch ( Throwable $e ) {
            $percent = 0;
        }

        if ( $html ) {
            return number_format( $percent, $precision, ',', '' ) . '%';
        }

        return number_format( $percent, $precision, '.', '' );
    }

    public static function toString( $valor = 0, $complemento = true ): string
    {
        $singular = [
            'centavo',
            'real',
            'mil',
            'milhão',
            'bilhão',
            'trilhão',
            'quatrilhão'
        ];
        $plural   = [
            'centavos',
            'reais',
            'mil',
            'milhões',
            'bilhões',
            'trilhões',
            'quatrilhões'
        ];

        $c   = [
            '',
            'cem',
            'duzentos',
            'trezentos',
            'quatrocentos',
            'quinhentos',
            'seiscentos',
            'setecentos',
            'oitocentos',
            'novecentos'
        ];
        $d   = [
            '',
            'dez',
            'vinte',
            'trinta',
            'quarenta',
            'cinquenta',
            'sessenta',
            'setenta',
            'oitenta',
            'noventa'
        ];
        $d10 = [
            'dez',
            'onze',
            'doze',
            'treze',
            'quatorze',
            'quinze',
            'dezesseis',
            'dezesete',
            'dezoito',
            'dezenove'
        ];
        $u   = [
            '',
            'um',
            'dois',
            'três',
            'quatro',
            'cinco',
            'seis',
            'sete',
            'oito',
            'nove'
        ];

        $z = 0;

        $valor   = number_format( $valor, 2, '.', '.' );
        $inteiro = explode( '.', $valor );
        for ( $i = 0; $i < count( $inteiro ); $i++ ) {
            for ( $ii = strlen( $inteiro[$i] ); $ii < 3; $ii++ ) {
                $inteiro[$i] = '0' . $inteiro[$i];
            }
        }

        $fim = count( $inteiro ) - ( $inteiro[count( $inteiro ) - 1] > 0 ? 1 : 2 );
        $rt  = '';
        for ( $i = 0; $i < count( $inteiro ); $i++ ) {
            $valor = $inteiro[$i];
            $rc    = ( ( $valor > 100 ) && ( $valor < 200 ) ) ? 'cento' : $c[$valor[0]];
            $rd    = ( $valor[1] < 2 ) ? '' : $d[$valor[1]];
            $ru    = ( $valor > 0 ) ? ( ( $valor[1] == 1 ) ? $d10[$valor[2]] : $u[$valor[2]] ) : '';

            $r = $rc . ( ( $rc && ( $rd || $ru ) ) ? ' e ' : '' ) . $rd . ( ( $rd && $ru ) ? ' e ' : '' ) . $ru;
            $t = count( $inteiro ) - 1 - $i;
            if ( $complemento ) {
                $r .= $r ? ' ' . ( $valor > 1 ? $plural[$t] : $singular[$t] ) : '';
                if ( $valor == '000' ) {
                    $z++;
                } elseif ( $z > 0 ) {
                    $z--;
                }
                if ( ( $t == 1 ) && ( $z > 0 ) && ( $inteiro[0] > 0 ) ) {
                    $r .= ( ( $z > 1 ) ? ' de ' : '' ) . $plural[$t];
                }
            }
            if ( $r ) {
                $rt = $rt . ( ( ( $i > 0 ) && ( $i <= $fim ) && ( $inteiro[0] > 0 ) && ( $z < 1 ) ) ? ( ( $i < $fim ) ? ', ' : ' e ' ) : ' ' ) . $r;
            }
        }

        return ( $rt ? trim( $rt ) : 'zero' );
    }

    public static function formatPercent( float $percent, $precision = 2 ): string
    {
        return number_format( $percent, $precision, ',', '' ) . '%';
    }

    public static function toGrowthPercent( $new, $old, $precision = 2, $html = true ): string
    {
        try {
            $percent = ( $new / $old * 100 ) - 100;
        } catch ( Throwable $e ) {
            $percent = 0;
        }

        if ( $html ) {
            if ( $percent < 0 ) {
                return '<span class="text-danger">' . number_format( $percent, $precision, ',', '' ) . '%</span>';
            }

            return '<span class="text-success">' . number_format( $percent, $precision, ',', '' ) . '%</span>';
        }

        return number_format( $percent, $precision, '.', '' );
    }
}
