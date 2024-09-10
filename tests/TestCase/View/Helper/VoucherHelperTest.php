<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\VoucherHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\VoucherHelper Test Case
 */
class VoucherHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\VoucherHelper
     */
    protected $Voucher;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Voucher = new VoucherHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Voucher);

        parent::tearDown();
    }
}
