<?php

namespace RedeCauzzoMais\Api;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RedeCauzzoMais\Jobs\TelegramJob;

class BoletoSicredi
{
    protected array  $config;
    protected string $prefixCache = '';

    const BASE_URL = 'https://api-parceiro.sicredi.com.br';

    const URL = [
        'auth'       => '/auth/openapi/token',
        'baixa'      => '/cobranca/boleto/v1/boletos/{nossoNumero}/baixa',
        'consulta'   => '/cobranca/boleto/v1/boletos/?codigoBeneficiario={codigoBeneficiario}&nossoNumero={nossoNumero}',
        'registro'   => '/cobranca/boleto/v1/boletos',
        'liquidados' => '/cobranca/boleto/v1/boletos/liquidados/dia?codigoBeneficiario={codigoBeneficiario}&dia={dia}&cpfCnpjBeneficiarioFinal={cpfCnpjBeneficiarioFinal}&pagina={pagina}',
        'vencimento' => '/cobranca/boleto/v1/boletos/{nossoNumero}/data-vencimento',
    ];

    public function __construct( array $config )
    {
        $this->config      = $config;
        $this->prefixCache = class_basename( __CLASS__ );
    }

    private function endpoint( string $url, ?array $params = null ): string
    {
        $url = self::URL[$url];

        if ( !empty( $params ) ) {
            $search = array_keys( $params );
            array_walk( $search, function ( &$value ) {
                $value = "{{$value}}";
            } );

            $url = str_replace( $search, $params, $url );
        }

        return self::BASE_URL . $url;
    }

    private function authToken()
    {
        echo var_export( __FUNCTION__, true );

        $token = Http::withHeaders( [
            'x-api-key' => $this->config['x-api-key'],
            'context'   => 'COBRANCA'
        ] )->asForm()->post( $this->endpoint( 'auth' ), [
            'username'   => $this->config['username'],
            'password'   => $this->config['password'],
            'scope'      => 'cobranca',
            'grant_type' => 'password'
        ] )->json();

        Cache::set( $this->prefixCache . 'AccessToken', $token['access_token'], $token['expires_in'] - 30 );
        Cache::set( $this->prefixCache . 'RefreshToken', $token['refresh_token'], $token['refresh_expires_in'] - 30 );

        return $token['access_token'];
    }

    private function authRefreshToken( $refreshToken )
    {
        echo var_export( __FUNCTION__, true );

        $token = Http::withHeaders( [
            'x-api-key' => $this->config['x-api-key'],
            'context'   => 'COBRANCA'
        ] )->asForm()->post( $this->endpoint( 'auth' ), [
            'refresh_token' => $refreshToken,
            'scope'         => 'cobranca',
            'grant_type'    => 'refresh_token'
        ] )->json();

        Cache::set( $this->prefixCache . 'AccessToken', $token['access_token'], $token['expires_in'] - 30 );
        Cache::set( $this->prefixCache . 'RefreshToken', $token['refresh_token'], $token['refresh_expires_in'] - 30 );

        return $token['access_token'];
    }

    public function getAccessToken()
    {
        $token = Cache::get( $this->prefixCache . 'AccessToken' );

        if ( !empty( $token ) ) {
            return $token;
        }

        $refreshToken = Cache::get( $this->prefixCache . 'RefreshToken' );

        if ( !empty( $refreshToken ) ) {
            return $this->authRefreshToken( $refreshToken );
        }

        return $this->authToken();
    }

    public function consulta( int $nossoNumero ): false|array
    {
        $endpoint = $this->endpoint( 'consulta', [
            'codigoBeneficiario' => $this->config['beneficiario'],
            'nossoNumero'        => $nossoNumero
        ] );

        $response = Http::withHeaders( [
            'x-api-key'   => $this->config['x-api-key'],
            'cooperativa' => $this->config['cooperativa'],
            'posto'       => $this->config['posto']
        ] )->withToken( $this->getAccessToken() )->get( $endpoint );

        if ( $response->status() <> 200 ) {
            TelegramJob::dispatch( implode( PHP_EOL, [
                'Code: ' . $response->status(),
                'Nosso Numero: ' . $nossoNumero,
                var_export( $response->json(), true )
            ] ) );

            return false;
        }

        return $response->json();
    }

    public function pedidoBaixa( int $nossoNumero ): false|array
    {
        $endpoint = $this->endpoint( 'baixa', [
            'nossoNumero' => $nossoNumero
        ] );

        $response = Http::withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withBody( '{}', 'application/json' )->withToken( $this->getAccessToken() )->patch( $endpoint );

        if ( $response->status() <> 202 ) {
            TelegramJob::dispatch( implode( PHP_EOL, [
                'Code: ' . $response->status(),
                'Nosso Numero: ' . $nossoNumero,
                var_export( $response->json(), true )
            ] ) );

            return false;
        }

        return $response->json();
    }

    public function liquidados( Carbon $dia, ?string $cpf = null, int $pagina = 0 ): false|array
    {
        $endpoint = $this->endpoint( 'liquidados', [
            'codigoBeneficiario'       => $this->config['beneficiario'],
            'dia'                      => $dia->format( 'd/m/Y' ),
            'cpfCnpjBeneficiarioFinal' => $cpf,
            'pagina'                   => $pagina
        ] );

        $response = Http::withHeaders( [
            'x-api-key'   => $this->config['x-api-key'],
            'cooperativa' => $this->config['cooperativa'],
            'posto'       => $this->config['posto']
        ] )->withToken( $this->getAccessToken() )->get( $endpoint );

        if ( $response->status() <> 200 ) {
            TelegramJob::dispatch( implode( PHP_EOL, [
                'Code: ' . $response->status(),
                'CPF: ' . $cpf,
                var_export( $response->json(), true )
            ] ) );

            return false;
        }

        $pagos = $response->json();

        if ( $pagos['hasNext'] ) {
            $pagos['items'] = array_merge( $pagos['items'], $this->liquidados( $dia, $cpf, $pagina + 1 ) );
        }

        return $pagos['items'];
    }

    public function novoVencimento( int $nossoNumero, Carbon $dia ): false|array
    {
        $endpoint = $this->endpoint( 'vencimento', [
            'nossoNumero' => $nossoNumero
        ] );

        $response = Http::withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withToken( $this->getAccessToken() )->asJson()->patch( $endpoint, [
            'dataVencimento' => $dia->format( 'Y-m-d' )
        ] );

        if ( $response->status() <> 202 ) {
            TelegramJob::dispatch( implode( PHP_EOL, [
                'Code: ' . $response->status(),
                'Nosso Numero: ' . $nossoNumero,
                var_export( $response->json(), true )
            ] ) );

            return false;
        }

        return $response->json();
    }

    public function registro( array $boleto ): false|array
    {
        $endpoint = $this->endpoint( 'registro' );
        $response = Http::withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withToken( $this->getAccessToken() )->asJson()->post( $endpoint, $boleto );

        if ( $response->status() <> 201 ) {
            TelegramJob::dispatch( implode( PHP_EOL, [
                'Code: ' . $response->status(),
                var_export( $response->json(), true )
            ] ) );

            return false;
        }

        return $response->json();
    }
}

