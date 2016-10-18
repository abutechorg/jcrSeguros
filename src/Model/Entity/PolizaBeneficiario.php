<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 12/9/2016
 * Time: 09:46
 */

use Cake\ORM\Entity;

class PolizaBeneficiario extends Entity{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true
    ];
}