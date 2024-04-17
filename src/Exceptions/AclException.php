<?php

namespace RedeCauzzoMais\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;

class AclException extends Exception
{
    protected $message = 'Acesso negado';
    protected $code    = 403;

    public function render( Request $request ): Response|JsonResponse
    {
        if ( $request->expectsJson() ) {
            return response()->json( [
                'message' => $this->message,
            ], $this->code );
        }

        return response()->view( 'errors.403', [
            'exception' => $this
        ], $this->code );
    }
}
