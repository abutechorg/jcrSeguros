<?php
namespace App\Model\Table;

use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */

class FinanciamientosTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('financiamientos');
        $this->primaryKey('financiamiento_id');
    }
}