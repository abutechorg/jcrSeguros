<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/10/2016
 * Time: 11:02 PM
 */

namespace App\Model\Table;


use Cake\ORM\Table;

class RamoCobertura extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('ramo_cobertura');
        $this->belongsTo("Cobertura",array('foreignKey'=>'cobertura_id'));
        $this->belongsTo("Ramo",array('foreignKey'=>'ramo_id'));
    }

}