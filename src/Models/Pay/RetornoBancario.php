<?php

namespace RedeCauzzoMais\Models\Pay;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\RetornoBancario
 *
 * @property int    $id_retorno_bancario
 * @property int    $id_pessoa_juridica
 * @property string $servico
 * @property string $retorno
 * @property mixed  $arquivo
 * @property int    $lido
 * @property string $created
 * @property string $updated
 * @method static Builder|RetornoBancario newModelQuery()
 * @method static Builder|RetornoBancario newQuery()
 * @method static Builder|RetornoBancario query()
 * @method static Builder|RetornoBancario whereArquivo( $value )
 * @method static Builder|RetornoBancario whereCreated( $value )
 * @method static Builder|RetornoBancario whereIdPessoaJuridica( $value )
 * @method static Builder|RetornoBancario whereIdRetornoBancario( $value )
 * @method static Builder|RetornoBancario whereLido( $value )
 * @method static Builder|RetornoBancario whereRetorno( $value )
 * @method static Builder|RetornoBancario whereServico( $value )
 * @method static Builder|RetornoBancario whereUpdated( $value )
 */
class RetornoBancario extends Model
{
    protected $connection = 'cauzzo';
    protected $table      = 'pay.retorno_bancario';
    protected $primaryKey = 'id_retorno_bancario';
    protected $fillable   = ['id_pessoa_juridica', 'servico', 'retorno', 'arquivo', 'lido', 'created', 'updated'];

    public $timestamps = false;

    //public function pessoaJuridica(): BelongsTo
    //{
    //    return $this->belongsTo( 'App\Model\PessoaJuridica', 'id_pessoa_juridica', 'id_pessoa_juridica' );
    //}
}
