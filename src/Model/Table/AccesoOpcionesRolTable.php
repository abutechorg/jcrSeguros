<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/8/2016
 * Time: 12:14
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class AccesoOpcionesRolTable extends Table{

    public function initialize(array $config){
        parent::initialize($config);
        $this->table('acceso_opciones_rol');
        $this->belongsTo('MenuAplicacion',array('foreignKey'=>'menu_id'));
    }

}