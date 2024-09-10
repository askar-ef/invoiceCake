<?php

declare(strict_types=1);

namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\ORM\TableRegistry;

class TransactionCodeComponent extends Component
{
    protected $Transactions;

    public function initialize(array $config): void
    {
        parent::initialize($config);
        // Load the Transactions model
        $this->Transactions = TableRegistry::getTableLocator()->get('Transactions');
    }

    public function generateCode(string $prefix = 'PRC', ?string $transactionDate = null): string
    {
        // Use the current date if no transaction date is provided
        if (!$transactionDate) {
            $transactionDate = date('Y-m-d H:i:s');
        }

        // Extract year and month from the transaction date
        $yearMonth = date('ym', strtotime($transactionDate));

        // Find the last transaction code with the same prefix and year-month
        $lastTransaction = $this->Transactions->find('all', [
            'conditions' => [
                'code LIKE' => $prefix . $yearMonth . '%'
            ],
            'order' => ['code' => 'DESC']
        ])->first();

        // Determine the next sequence number
        if ($lastTransaction) {
            $lastSequence = (int)substr($lastTransaction->code, -4);
            $nextSequence = str_pad((string)($lastSequence + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $nextSequence = '0001';
        }

        // Return the generated transaction code
        return $prefix . $yearMonth . $nextSequence;
    }
}
