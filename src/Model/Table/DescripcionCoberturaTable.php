<?php
/**
 * Created by PhpStorm.
 * User: Eduardo Luttinger
 * Date: 05/10/2016
 * Time: 11:07 PM
 */

namespace App\Model\Table;


use Cake\ORM\Table;

class DescripcionCoberturaTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->table('descripcion_cobertura');
        $this->primaryKey("descripcion_cobertura_id");

    }

}