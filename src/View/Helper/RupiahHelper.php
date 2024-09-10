<?php

declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper;
use Cake\View\View;

/**
 * Rupiah helper
 */
class RupiahHelper extends Helper
{


    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    public function formatRupiah($number)
    {
        return 'Rp' . number_format($number, 0, ',', '.');
    }
    protected $_defaultConfig = [];
}
