<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/9/2016
 * Time: 02:24
 */


namespace App\Model\Table;

use Cake\ORM\Table;

class SiniestroRepuestoTable extends Table{


    public function initialize(array $config){
        parent::initialize($config);

        $this->table('siniestro_repuesto');
        $this->belongsTo('Repuestos',array('foreignKey'=>'repuesto_id'));
    }


}