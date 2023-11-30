<?php

namespace RedeCauzzoMais\Logger;

use Monolog\Logger;

class DatabaseLogger
{
    public function __invoke( array $config ): Logger
    {
        $handler = new DatabaseHandler();
        $handler->setConnection( config( 'cauzzo.log.connection' ) );
        $handler->setSystemName( config( 'cauzzo.log.system-name' ) );
        $handler->setDefaultSigla( config( 'cauzzo.log.sigla' ) );

        return new Logger( 'Database', [$handler] );
    }
}
