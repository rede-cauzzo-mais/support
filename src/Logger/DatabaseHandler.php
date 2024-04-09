<?php

namespace RedeCauzzoMais\Logger;

use Illuminate\Support\Facades\Request;
use Monolog\Handler\AbstractProcessingHandler;
use RedeCauzzoMais\Models\Cauzzo\Log;
use RedeCauzzoMais\Support\Browser;
use Throwable;

class DatabaseHandler extends AbstractProcessingHandler
{
    private string  $connection;
    private string  $systemName;
    private ?string $defaultSigla = null;

    public function setConnection( $connection ): void
    {
        $this->connection = $connection;
    }

    public function setSystemName( $systemName ): void
    {
        $this->systemName = $systemName;
    }

    public function setDefaultSigla( $systemName ): void
    {
        $this->defaultSigla = $systemName;
    }

    protected function write( $record ): void
    {
        /* fix para atualização do monolog */
        if ( !is_array( $record ) ) {
            $record = $record->toArray();
        }

        $exception = $record['context']['exception'] ?? null;

        if ( $exception instanceof Throwable ) {
            $record['context']['exception'] = (string) $exception;
        }

        $message = preg_replace( '#<br\s*/?>#i', PHP_EOL, $record['message'] ?? '' );
        $message = strip_tags( $message );

        ( new Log( [
            'id_user'    => auth()->id() ?? null,
            'name'       => auth()->user()->nome ?? null,
            'sigla'      => session( 'sigla', $this->defaultSigla ),
            'system'     => $this->systemName,
            'route'      => Request::getRequestUri(),
            'request'    => Request::getMethod(),
            'level'      => $record['level'],
            'level_name' => $record['level_name'],
            'message'    => $message,
            'context'    => json_encode( $record['context'] ),
            'ip'         => Request::ip(),
            'platform'   => Browser::platformName(),
            'browser'    => Browser::browserName()
        ] ) )->setConnection( $this->connection )->save();
    }
}
