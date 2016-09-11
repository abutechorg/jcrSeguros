<?php
namespace App\Model\Table;

use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */

class TipoPolizaTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('tipo_poliza');
        $this->primaryKey('tipo_poliza_id');
    }
}