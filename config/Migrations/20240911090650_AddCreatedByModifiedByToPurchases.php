<?php

declare(strict_types=1);

use Migrations\AbstractMigration;

class AddCreatedByModifiedByToPurchases extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     * @return void
     */
    public function change()
    {
        $table = $this->table('purchases');
        $table->addColumn('created_by', 'integer', ['null' => true])
            ->addColumn('modified_by', 'integer', ['null' => true])
            ->addForeignKey('created_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ])
            ->addForeignKey('modified_by', 'users', 'id', [
                'delete' => 'SET_NULL',
                'update' => 'NO_ACTION',
            ])
            ->update();
    }
}
