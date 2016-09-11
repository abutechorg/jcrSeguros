<?php
namespace App\Model\Table;

use Cake\ORM\Table;
/**
 * Created by PhpStorm.
 * User: EduardoDeLaCruz
 * Date: 22/8/2016
 * Time: 08:31
 */

class AseguradoraTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('aseguradora');
        $this->primaryKey('aseguradora_id');
    }
}