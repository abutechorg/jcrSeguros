<?php
namespace App\Model\Table;

use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */

class VehiculoTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('vehiculo');
        $this->primaryKey('vehiculo_id');
        $this->belongsTo('MarcaVehiculo',array('foreignKey'=>'vehiculo_marca_id'));
    }
}