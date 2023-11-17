<?php

namespace RedeCauzzoMais\Api;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

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
        $token = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
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
        $token = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
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

    /**
     * @throws \RedeCauzzoMais\Api\ApiException
     */
    public function consulta( int $nossoNumero ): false|array
    {
        $endpoint = $this->endpoint( 'consulta', [
            'codigoBeneficiario' => $this->config['beneficiario'],
            'nossoNumero'        => $nossoNumero
        ] );

        $response = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
            'x-api-key'   => $this->config['x-api-key'],
            'cooperativa' => $this->config['cooperativa'],
            'posto'       => $this->config['posto']
        ] )->withToken( $this->getAccessToken() )->get( $endpoint );

        $result = $response->json();

        if ( $response->status() <> 200 ) {
            throw new ApiException( $result['message'] ?? 'Erro inesperado', $response->status(), $result );
        }

        return $result;
    }

    /**
     * @throws \RedeCauzzoMais\Api\ApiException
     */
    public function pedidoBaixa( int $nossoNumero ): false|array
    {
        $endpoint = $this->endpoint( 'baixa', [
            'nossoNumero' => $nossoNumero
        ] );

        $response = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withBody( '{}', 'application/json' )->withToken( $this->getAccessToken() )->patch( $endpoint );

        $result = $response->json();

        if ( $response->status() <> 202 ) {
            throw new ApiException( $result['message'] ?? 'Erro inesperado', $response->status(), $result );
        }

        return $result;
    }

    /**
     * @throws \RedeCauzzoMais\Api\ApiException
     */
    public function liquidados( Carbon $dia, ?string $cpf = null, int $pagina = 0 ): false|array
    {
        $endpoint = $this->endpoint( 'liquidados', [
            'codigoBeneficiario'       => $this->config['beneficiario'],
            'dia'                      => $dia->format( 'd/m/Y' ),
            'cpfCnpjBeneficiarioFinal' => $cpf,
            'pagina'                   => $pagina
        ] );

        $response = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
            'x-api-key'   => $this->config['x-api-key'],
            'cooperativa' => $this->config['cooperativa'],
            'posto'       => $this->config['posto']
        ] )->withToken( $this->getAccessToken() )->get( $endpoint );

        $pagos = $response->json();

        if ( $response->status() <> 200 ) {
            throw new ApiException( $pagos['message'] ?? 'Erro inesperado', $pagos->code, $pagos );
        }

        if ( $pagos['hasNext'] ) {
            $pagos['items'] = array_merge( $pagos['items'], $this->liquidados( $dia, $cpf, $pagina + 1 ) );
        }

        return $pagos['items'];
    }

    /**
     * @throws \RedeCauzzoMais\Api\ApiException
     */
    public function novoVencimento( int $nossoNumero, Carbon $dia ): false|array
    {
        $endpoint = $this->endpoint( 'vencimento', [
            'nossoNumero' => $nossoNumero
        ] );

        $response = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withToken( $this->getAccessToken() )->asJson()->patch( $endpoint, [
            'dataVencimento' => $dia->format( 'Y-m-d' )
        ] );

        $result = $response->json();

        if ( $response->status() <> 202 ) {
            throw new ApiException( $result['message'] ?? 'Erro inesperado', $response->status(), $result );
        }

        return $result;
    }

    /**
     * @throws \RedeCauzzoMais\Api\ApiException
     */
    public function registro( array $boleto ): false|array
    {
        $endpoint = $this->endpoint( 'registro' );

        $response = Http::retry( 3, 250, function ( $exception ) {
            return $exception instanceof ConnectionException;
        }, false )->withHeaders( [
            'x-api-key'          => $this->config['x-api-key'],
            'cooperativa'        => $this->config['cooperativa'],
            'posto'              => $this->config['posto'],
            'codigoBeneficiario' => $this->config['beneficiario'],
        ] )->withToken( $this->getAccessToken() )->asJson()->post( $endpoint, $boleto );

        $result = $response->json();

        if ( $response->status() <> 201 ) {
            throw new ApiException( $result['message'] ?? 'Erro inesperado', $response->status(), $result );
        }

        return $result;
    }
}
