<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Purchase Entity
 *
 * @property int $id
 * @property int $supplier_id
 * @property \Cake\I18n\FrozenTime $purchase_date
 * @property int $amount
 *
 * @property \App\Model\Entity\Supplier $supplier
 */
class Purchase extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected $_accessible = [
        'supplier_id' => true,
        'purchase_date' => true,
        'amount' => true,
        'supplier' => true,
    ];
}
