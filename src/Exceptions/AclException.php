<?php

namespace RedeCauzzoMais\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AclException extends HttpException
{
	public function __construct( string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [] )
	{
		parent::__construct( 403, $message, $previous, $headers, $code );
	}

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
