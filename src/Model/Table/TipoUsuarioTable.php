<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/8/2016
 * Time: 06:40
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class TipoUsuarioTable extends Table{


    public function initialize(array $config){

        parent::initialize($config);
        $this->table('tipo_usuario');
        $this->primaryKey("tipo_usuario_id");

    }

}