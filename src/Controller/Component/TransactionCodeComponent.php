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

    public function generateCode(string $prefix = 'PRC', string $transactionDate): string
    {
        $yearMonth = date('ym', strtotime($transactionDate));

        $lastTransaction = $this->Transactions->find('all', [
            'conditions' => ['code LIKE' => $prefix . $yearMonth . '%'],
            'order' => ['code' => 'DESC']
        ])->first();

        $nextSequence = $lastTransaction ?
            str_pad((string)((int)substr($lastTransaction->code, -4) + 1), 4, '0', STR_PAD_LEFT) :
            '0001';

        return $prefix . $yearMonth . $nextSequence;
    }
}
