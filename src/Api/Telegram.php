<?php

namespace RedeCauzzoMais\Api;

use Illuminate\Support\Facades\Http;

class Telegram
{
    public static int $timeout = 0;

    const CHAT_DEBUG = 'debug';
    const CHAT_ALERT = 'alert';

    public static function sendMessage( string $message, string|int|null $chatId = null ): bool
    {
        $endpoint = config( 'cauzzo.telegram.endpoint' );
        $token    = config( 'cauzzo.telegram.token' );

        if ( is_null( $chatId ) or $chatId == self::CHAT_DEBUG ) {
            $chatId = config( 'cauzzo.telegram.chat_debug' );
        } elseif ( $chatId == self::CHAT_ALERT ) {
            $chatId = config( 'cauzzo.telegram.chat_alert' );
        }

        $title   = config( 'app.name' );
        $message = str_replace( '<br>', "\n", $message );
        $message = "<b>$title</b>\n" . $message;

        if ( mb_strlen( $message ) > 4096 ) {
            $message = substr( $message, 0, 4096 );
        }

        $response = Http::timeout( static::$timeout )->get( $endpoint . $token . '/sendMessage', [
            'parse_mode' => 'HTML',
            'text'       => $message,
            'chat_id'    => $chatId
        ] );

        return (bool) $response->status();
    }
}
