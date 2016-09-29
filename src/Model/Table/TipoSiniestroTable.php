<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/9/2016
 * Time: 02:23
 */


namespace App\Model\Table;

use Cake\ORM\Table;

class TipoSiniestroTable extends Table{


    public function initialize(array $config){
        parent::initialize($config);

        $this->table('tipo_siniestro');
        $this->primaryKey('tipo_siniestro_id');
    }

}