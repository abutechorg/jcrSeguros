<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 11/10/2016
 * Time: 02:12
 */

namespace App\Model\Table;

use Cake\ORM\Table;

class MarcaVehiculo extends Table{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('marca_vehiculo');
        $this->primaryKey('marca_vehiculo_id');
    }

}

