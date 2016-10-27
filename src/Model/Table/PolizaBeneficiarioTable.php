<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 12/9/2016
 * Time: 09:47
 */

namespace App\Model\Table;
use Cake\ORM\Table;

class PolizaBeneficiarioTable extends Table{

    public function initialize(array $config){
        parent::initialize($config);
        $this->table('poliza_beneficiario');
        $this->belongsTo("Usuarios",array('foreignKey'=>'usuario_id'));
    }
}