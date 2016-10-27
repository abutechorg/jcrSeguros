<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 12/10/2016
 * Time: 12:06
 */

namespace App\Model\Table;

use Cake\ORM\Table;

class PolizaVehiculoTable extends Table{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('poliza_vehiculo');
        $this->belongsTo('Vehiculo',array('foreignKey'=>'vehiculo_id'));
    }

}