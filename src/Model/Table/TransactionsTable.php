<?php

declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Behavior\TableBehavior;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class TransactionsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');

        $this->setTable('transactions');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Customers', [
            'foreignKey' => 'customer_id',
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

        $this->addBehavior(TableBehavior::class);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('customer_id')
            ->notEmptyString('customer_id');

        $validator
            ->dateTime('transaction_date')
            ->requirePresence('transaction_date', 'create')
            ->notEmptyDateTime('transaction_date');

        $validator
            ->integer('amount')
            ->requirePresence('amount', 'create')
            ->notEmptyString('amount');

        $validator
            ->scalar('code')
            ->maxLength('code', 255)
            ->requirePresence('code', 'create')
            // ->notEmptyString('code');
            ->allowEmptyString('code', null, 'create');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('customer_id', 'Customers'), ['errorField' => 'customer_id']);
        $rules->add($rules->existsIn('created_by', 'CreatedByUsers'), ['errorField' => 'created_by']);
        $rules->add($rules->existsIn('modified_by', 'ModifiedByUsers'), ['errorField' => 'modified_by']);

        return $rules;
    }
}
