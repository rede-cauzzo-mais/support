<?php

namespace RedeCauzzoMais\Api;

use Exception;

class ApiException extends Exception
{
    public function __construct( $message = "", $code = 0, protected mixed $context = [] )
    {
        parent::__construct( $message, $code );
    }

    public function getContext()
    {
        return $this->context;
    }
}
