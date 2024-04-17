<?php

namespace RedeCauzzoMais\Exceptions;

use Exception;

class UnprocessableException extends Exception
{
    protected $code = 422;
}
