<?php
/**
 * Created by PhpStorm.
 * User: Gualdo de la cruz
 * Date: 05/10/2016
 * Time: 10:47 AM
 */

namespace App\Model\Table;

use Cake\ORM\Table;

class TipoClienteTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('tipo_cliente');
        $this->primaryKey('tipo_cliente_id');
    }

}