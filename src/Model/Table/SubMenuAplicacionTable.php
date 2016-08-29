<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/8/2016
 * Time: 12:11
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class SubMenuAplicacionTable extends Table{


    public function initialize(array $config){
        parent::initialize($config);
        $this->table('sub_menu_aplicacion');
        $this->primaryKey('sub_menu_id');

    }
}