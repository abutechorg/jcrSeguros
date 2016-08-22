<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 11/8/2016
 * Time: 09:56
 */
namespace App\Model\Table;
use Cake\ORM\Table;

class DepartamentTable extends Table{

    public function initialize(array $config){
        parent::initialize($config);
        $this->table('departments');
        $this->primaryKey('departament_id');
        $this->belongsTo('Business', array('foreignKey' => 'business_id'));
    }

}