<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 23/8/2016
 * Time: 11:13
 */
namespace App\Model\Table;
use Cake\ORM\Table;


class PolizaTable extends Table{


    public function initialize(array $config){

        parent::initialize($config);
        $this->table('poliza');
        $this->primaryKey("poliza_id");
        $this->belongsTo("Aseguradora",array('foreignKey'=>'aseguradora_id'));
        $this->belongsTo("Ramo",array('foreignKey'=>'ramo_id'));


    }
}

