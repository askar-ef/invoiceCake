<?php
declare(strict_types=1);

namespace App\Test\TestCase\Controller\Component;

use App\Controller\Component\TransactionCodeComponent;
use Cake\Controller\ComponentRegistry;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\Component\TransactionCodeComponent Test Case
 */
class TransactionCodeComponentTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Controller\Component\TransactionCodeComponent
     */
    protected $TransactionCode;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $registry = new ComponentRegistry();
        $this->TransactionCode = new TransactionCodeComponent($registry);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->TransactionCode);

        parent::tearDown();
    }
}
