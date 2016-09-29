<?php
/**
 * Created by PhpStorm.
 * User: VladimirIlich
 * Date: 28/9/2016
 * Time: 02:13
 */

namespace App\Model\Entity;

use Cake\ORM\Entity;

class SiniestroRepuesto extends Entity{


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