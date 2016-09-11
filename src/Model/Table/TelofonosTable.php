<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 10/9/2016
 * Time: 10:39
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class TelefonoTable extends Table{

    public function initialize(array $config){

        parent::initialize($config);
        $this->table('telefonos');
        $this->primaryKey("telefono_id");

        $this->belongsTo('Usuarios',
            array('targetForeignKey' => 'usuario_id',
                'foreignKey' => 'telefono_id',
                'joinTable' => 'usuarios_telefonos'));
    }

}