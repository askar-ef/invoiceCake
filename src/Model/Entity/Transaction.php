<?php

declare(strict_types=1);

// src/Model/Entity/Transaction.php
// namespace App\Model\Entity;

// use Cake\ORM\Entity;

// class Transaction extends Entity
// {
//     protected $_accessible = [
//         '*' => true,
//         'id' => false,
//         'code' => true,
//     ];

//     protected $_virtual = ['voucher'];

//     protected function _getVoucher()
//     {
//         if ($this->amount > 30000000) {
//             return 'Voucher Hotel Santika';
//         } else {
//             return 'Voucher Belanja Indomaret';
//         }
//     }
// }


namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Transaction Entity
 *
 * @property int $id
 * @property int $customer_id
 * @property int $amount
 * @property string $code
 *
 * @property \App\Model\Entity\Customer $customer
 */
class Transaction extends Entity
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
        'customer_id' => true,
        'transaction_date' => true,
        'amount' => true,
        'code' => true,
        'customer' => true,
    ];
}
