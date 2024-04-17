<?php

namespace RedeCauzzoMais\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class UnprocessableException extends HttpException
{
	public function __construct( string $message = '', ?\Throwable $previous = null, int $code = 0, array $headers = [] )
	{
		parent::__construct( 422, $message, $previous, $headers, $code );
	}
}
