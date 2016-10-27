<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/8/2016
 * Time: 12:08
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class MenuAplicacionTable extends Table{


    public function initialize(array $config){
        parent::initialize($config);
        $this->table('menu_aplicacion');
        $this->primaryKey('menu_id');
        $this->hasMany('SubMenuAplicacion',array('foreignKey'=>'menu_id'));

    }
}