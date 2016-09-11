<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 10/9/2016
 * Time: 10:47
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class UsuariosTelefonosTable extends Table{

    public function initialize(array $config){

        parent::initialize($config);
        $this->table('usuarios_telefonos');

    }
}