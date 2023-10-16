
<p align="center">
<a href="https://cauzzomais.com.br" target="_blank"><img src="https://user-images.githubusercontent.com/38639869/231880956-dd849790-c4b4-4c57-80b0-66a21ed4748e.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://packagist.org/packages/rede-cauzzo-mais/support"><img src="https://img.shields.io/packagist/dt/rede-cauzzo-mais/support" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/rede-cauzzo-mais/support"><img src="https://img.shields.io/packagist/v/rede-cauzzo-mais/support" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/rede-cauzzo-mais/support"><img src="https://img.shields.io/packagist/l/rede-cauzzo-mais/support" alt="License"></a>
</p>

## Introduction
Package designed to meet the needs of __Rede Cauzzo Mais__ projects, in this library you will find support functions, helpers and other useful things.

## Install
To install, just run:

    composer require rede-cauzzo-mais/support

#### Telegram env
    TELEGRAM_ENDPOINT=
    TELEGRAM_TOKEN=
    TELEGRAM_CHAT_ID=

#### Telegram config
    return [
        'telegram'    => [
        'endpoint' => env( 'TELEGRAM_ENDPOINT' ),
        'token'    => env( 'TELEGRAM_TOKEN' ),
        'chat_id'  => env( 'TELEGRAM_CHAT_ID' )
        ]
    ];

#### 
