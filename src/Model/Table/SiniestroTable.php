<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 23/8/2016
 * Time: 11:28
 */
namespace App\Model\Table;
use Cake\ORM\Table;

class SiniestroTable extends Table{

    public function initialize(array $config){

        parent::initialize($config);
        $this->table('siniestro');
        $this->primaryKey("siniestro_id");
        $this->belongsTo('Poliza',array('foreignKey'=>'poliza_id'));
        $this->belongsTo('Ramo',array('foreignKey'=>'ramo_id'));
        $this->belongsTo('Usuarios',array('foreignKey'=>'usuario_id'));

    }
}