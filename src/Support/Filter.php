<?php

namespace RedeCauzzoMais\Support;

class Filter
{
    public static function toChars( $string )
    {
        return preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $string ) );
    }

    public static function numericEscape( string $text, string $extra = '' ): string
    {
        return preg_replace( "/[^0-9{$extra}]/i", '', $text );
    }

    public static function alphaEscape( string $text, string $extra = '' ): string
    {
        return preg_replace( "/[^a-z{$extra}]/i", '', $text );
    }

    public static function alphaSpecialEscape( string $text, string $extra = '' ): string
    {
        return preg_replace( "/[^a-zÀÁÂÃÇÈÉÊÌÍÎÒÓÔÕÙÚÛàáâãçèéêìíîñòóôõùúû{$extra}]/i", '', $text );
    }

    public static function alphaNumericEscape( string $text, string $extra = '' ): string
    {
        return preg_replace( "/[^a-z0-9{$extra}]/i", '', $text ) ?? '';
    }

    public static function alphaNumericSpecialEscape( string $text, string $extra = '' ): string
    {
        return preg_replace( "/[^a-z0-9ÀÁÂÃÇÈÉÊÌÍÎÒÓÔÕÙÚÛàáâãçèéêìíîñòóôõùúû{$extra}]/i", '', $text );
    }

    public static function removeDoubleLineBreak( ?string $string = '' )
    {
        return preg_replace( '/<br\s?\/?>(\s*<br\s?\/?>)+/i', '<br>', $string );
    }

    public static function allTrim( ?string $string = '' )
    {
        return preg_replace( '/\s+/', ' ', trim( $string ) );
    }

    public static function toLower( $string )
    {
        return mb_strtolower( $string, 'utf8' );
    }
}

