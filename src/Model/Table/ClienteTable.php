<?php
/**
 * Created by PhpStorm.
 * User: Gualdo De La Cruz
 * Date: 05/10/2016
 * Time: 10:44 AM
 */

namespace App\Model\Table;


use Cake\ORM\Table;

class ClienteTable extends Table
{


    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('clientes');
        $this->primaryKey('cliente_id');
        $this->belongsTo("TipoCliente",array('foreignKey'=>'tipo_cliente_id'));
    }


}