<?php
return [
    'log'      => [
        'connection'  => env( 'LOG_DB_CONNECTION' ),
        'system-name' => env( 'LOG_DB_SYSTEM_NAME' ),
        'sigla'       => env( 'LOG_DB_DEFAULT_SIGLA' )
    ],
    'telegram' => [
        'endpoint'   => env( 'TELEGRAM_ENDPOINT' ),
        'token'      => env( 'TELEGRAM_TOKEN' ),
        'chat_debug' => env( 'TELEGRAM_CHAT_DEBUG' ),
        'chat_alert' => env( 'TELEGRAM_CHAT_ALERT' ),
    ],
    'sicredi'  => [
        'boleto' => [
            'x-api-key'    => env( 'SICREDI_BOLETO_XAPIKEY' ),
            'username'     => env( 'SICREDI_BOLETO_USERNAME' ),
            'password'     => env( 'SICREDI_BOLETO_PASSWORD' ),
            'cooperativa'  => env( 'SICREDI_BOLETO_COOPERATIVA' ),
            'posto'        => env( 'SICREDI_BOLETO_POSTO' ),
            'beneficiario' => env( 'SICREDI_BOLETO_BENEFICIARIO' ),
        ]
    ]
];
