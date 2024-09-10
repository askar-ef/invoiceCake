<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class CreateSuppliers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('suppliers');
        $table->addColumn('name', 'string', ['limit' => 255])
            ->addColumn('address', 'text', ['null' => true])
            ->addColumn('phone', 'string', ['limit' => 20, 'null' => true])
            ->create();
    }
}
