<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/10/2016
 * Time: 06:02 PM
 */

namespace App\Model\Table;


use Cake\ORM\Table;

class PolizaCoberturaTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('poliza_coberturas');
        $this->primaryKey('poliza_coberturas_id');
        $this->belongsTo("Poliza",array('foreignKey'=>'poliza_id'));
        $this->belongsTo("Cobertura",array('foreignKey'=>'cobertura_id'));
        $this->belongsTo("DescripcionCobertura",array('foreignKey'=>'descripcion_cobertura_id'));

    }

}