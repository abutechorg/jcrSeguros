<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 5/10/2016
 * Time: 11:27
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class TipoClienteTable extends Table{

    public function initialize(array $config){

        parent::initialize($config);
        $this->table('tipo_cliente');
        $this->primaryKey("tipo_cliente_id");

    }

}