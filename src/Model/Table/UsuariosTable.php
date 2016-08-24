<?php
namespace App\Model\Table;
use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 22/8/2016
 * Time: 08:31
 */

class UsuariosTable extends Table{


    public function initialize(array $config){

        parent::initialize($config);
        $this->table('usuarios');
        $this->primaryKey("usuario_id");

    }

}