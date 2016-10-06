<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 5/10/2016
 * Time: 11:21
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class ClientesTable extends Table{

    public function initialize(array $config){

        parent::initialize($config);
        $this->table('clientes');
        $this->primaryKey("cliente_id");
        $this->belongsTo('TipoCliente',array('foreignKey'=>'tipo_cliente_id'));

    }

}