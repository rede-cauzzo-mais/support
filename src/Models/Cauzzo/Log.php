<?php

namespace RedeCauzzoMais\Models\Cauzzo;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

/**
 * RedeCauzzoMais\Models\Cauzzo\Log
 *
 * @property int         $id_log
 * @property int|null    $id_user
 * @property string|null $name
 * @property string|null $sigla
 * @property string|null $system
 * @property string|null $route
 * @property string|null $request
 * @property string      $level
 * @property string      $level_name
 * @property string      $message
 * @property string|null $context
 * @property string|null $ip
 * @property string|null $platform
 * @property string|null $browser
 * @property string      $created
 * @property string      $updated
 * @method static Builder|Log newModelQuery()
 * @method static Builder|Log newQuery()
 * @method static Builder|Log query()
 * @method static Builder|Log whereBrowser( $value )
 * @method static Builder|Log whereContext( $value )
 * @method static Builder|Log whereCreated( $value )
 * @method static Builder|Log whereIdLog( $value )
 * @method static Builder|Log whereIdUser( $value )
 * @method static Builder|Log whereIp( $value )
 * @method static Builder|Log whereLevel( $value )
 * @method static Builder|Log whereLevelName( $value )
 * @method static Builder|Log whereMessage( $value )
 * @method static Builder|Log whereName( $value )
 * @method static Builder|Log wherePlatform( $value )
 * @method static Builder|Log whereRequest( $value )
 * @method static Builder|Log whereRoute( $value )
 * @method static Builder|Log whereSigla( $value )
 * @method static Builder|Log whereSystem( $value )
 * @method static Builder|Log whereUpdated( $value )
 * @mixin \Eloquent
 */
class Log extends Model
{
    protected $table      = 'log';
    protected $primaryKey = 'id_log';
    protected $fillable   = [
        'id_user',
        'name',
        'sigla',
        'system',
        'route',
        'request',
        'level',
        'level_name',
        'message',
        'context',
        'ip',
        'platform',
        'browser',
        'created',
        'updated'
    ];

    public $timestamps = false;
}
