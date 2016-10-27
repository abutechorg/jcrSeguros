<?php
namespace App\Model\Table;

use Cake\ORM\Table;

/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */
class RamoTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('ramo');
        $this->primaryKey('ramo_id');
        $this->belongsToMany('Coberturas',
            array('targetForeignKey' => 'cobertura_id',
                'foreignKey' => 'ramo_id',
                'joinTable' => 'ramo_cobertura'));
    }
}