<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreatePurchases extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('purchases');
        $table->addColumn('supplier_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ])
            ->addColumn('purchase_date', 'datetime', [
                'default' => null,
                'null' => false,
            ])
            ->addColumn('amount', 'integer', [
                'default' => null,
                'limit' => 11,
                'null' => false,
            ])
            ->addForeignKey('supplier_id', 'suppliers', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION'
            ])
            ->create();
    }
}
