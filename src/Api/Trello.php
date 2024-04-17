<?php

namespace RedeCauzzoMais\Api;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Request;
use Throwable;

class Trello
{
    public static int $timeout = 0;

    const ENDPOINT = [
        'get_labels'  => 'https://api.trello.com/1/boards/{idBoard}/labels/',
        'create_card' => 'https://api.trello.com/1/cards'
    ];

    public static function createCard( string $name, string $desc, string $pos = 'top', array $moreParams = [] ): bool
    {
        try {
            $key   = config( 'cauzzo.trello.key' );
            $token = config( 'cauzzo.trello.token' );
            $list  = config( 'cauzzo.trello.list' );

            $response = Http::timeout( static::$timeout )->throw()->post( self::ENDPOINT['create_card'], [
                'idList' => $list,
                ...compact( 'name', 'desc', 'pos', 'key', 'token' ),
                ...$moreParams,
            ] );

            return $response->status() == 200;
        } catch ( Throwable ) {
            return false;
        }
    }

    public static function getLabels( ?string $labelName = null ): null|array|string
    {
        try {
            $cacheName = 'trelloLabels' . sha1( __CLASS__ );

            $labels = Cache::get( $cacheName );

            if ( !empty( $labels ) ) {
                return empty( $labelName ) ? $labels : ( $labels[$labelName] ?? null );
            }

            $key   = config( 'cauzzo.trello.key' );
            $token = config( 'cauzzo.trello.token' );
            $board = config( 'cauzzo.trello.board' );

            $endpoint = str_replace( '{idBoard}', $board, self::ENDPOINT['get_labels'] );

            $labels = Http::timeout( static::$timeout )->throw()->get( $endpoint, [
                'key'   => $key,
                'token' => $token
            ] )->json();

            $labels = array_combine( array_column( $labels, 'name' ), array_column( $labels, 'id' ) );

            Cache::set( $cacheName, $labels, now()->addDays( 7 ) );

            return empty( $labelName ) ? $labels : ( $labels[$labelName] ?? null );
        } catch ( Throwable ) {
            return [];
        }
    }

    public static function report( Throwable $e ): void
    {
        Trello::createCard( $e->getMessage(), implode( PHP_EOL, [
            '💻 **Local:** ' . config( 'app.name' ),
            '🤷🏻 **User:** ' . Auth::user()?->id_user . ' - ' . Auth::user()?->nome,
            '🌐 **Route:** ' . Request::getRequestUri(),
            '📁 **File:** ' . str_replace( base_path(), '', $e->getFile() ),
            '🧵 **Line:** ' . $e->getLine(),
            '',
            array_reverse( explode( '\\', get_class( $e ) ) )[0],
            '`' . $e->getMessage() . '`'
        ] ), moreParams: ['idLabels' => Trello::getLabels( config( 'app.name' ) )] );
    }
}
