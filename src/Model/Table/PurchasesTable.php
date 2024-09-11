<?php

declare(strict_types=1);

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class PurchasesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');

        $this->setTable('purchases');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // BelongsTo relationship with Suppliers
        $this->belongsTo('Suppliers', [
            'foreignKey' => 'supplier_id',
            'joinType' => 'INNER',
        ]);

        // BelongsTo relationship with Users for the created_by and modified_by fields
        $this->belongsTo('Users', [
            'foreignKey' => 'created_by',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'modified_by',
            'joinType' => 'INNER',
        ]);

        // Use 'CreatedByUsers' and 'ModifiedByUsers' for the associations
        $this->belongsTo('CreatedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'created_by',
            'propertyName' => 'createdByUser',
        ]);

        $this->belongsTo('ModifiedByUsers', [
            'className' => 'Users',
            'foreignKey' => 'modified_by',
            'propertyName' => 'modifiedByUser',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('supplier_id')
            ->notEmptyString('supplier_id');

        $validator
            ->dateTime('purchase_date')
            ->requirePresence('purchase_date', 'create')
            ->notEmptyDateTime('purchase_date');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('supplier_id', 'Suppliers'), ['errorField' => 'supplier_id']);
        $rules->add($rules->existsIn('created_by', 'CreatedByUsers'), ['errorField' => 'created_by']);
        $rules->add($rules->existsIn('modified_by', 'ModifiedByUsers'), ['errorField' => 'modified_by']);

        return $rules;
    }
}
