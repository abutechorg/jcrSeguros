<?php
namespace App\Model\Table;

use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */

class CoberturaTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('cobertura');
        $this->primaryKey('cobertura_id');
        $this->belongsToMany('DescripcionCobertura',
            array('targetForeignKey' => 'descripcion_cobertura_id',
                'foreignKey' => 'cobertura_id',
                'joinTable' => 'cobertura_descripcion_cobertura;'));
    }
}