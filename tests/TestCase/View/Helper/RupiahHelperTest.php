<?php
declare(strict_types=1);

namespace App\Test\TestCase\View\Helper;

use App\View\Helper\RupiahHelper;
use Cake\TestSuite\TestCase;
use Cake\View\View;

/**
 * App\View\Helper\RupiahHelper Test Case
 */
class RupiahHelperTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\View\Helper\RupiahHelper
     */
    protected $Rupiah;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $view = new View();
        $this->Rupiah = new RupiahHelper($view);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Rupiah);

        parent::tearDown();
    }
}
