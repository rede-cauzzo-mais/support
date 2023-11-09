<?php
return [
    'telegram' => [
        'endpoint' => env( 'TELEGRAM_ENDPOINT' ),
        'token'    => env( 'TELEGRAM_TOKEN' ),
        'chat_id'  => env( 'TELEGRAM_CHAT_ID' )
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
