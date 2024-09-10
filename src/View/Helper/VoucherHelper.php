<?php

declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;

class VoucherHelper extends Helper
{
    public function getVoucher($totalAmount): string
    {
        if ($totalAmount > 30000000) {
            return 'Voucher Hotel Santika';
        } else {
            return 'Voucher Belanja Indomaret';
        }
    }
}
