<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Log\Log;

/**
 * Transactions Controller
 *
 * @property \App\Model\Table\TransactionsTable $Transactions
 * @method \App\Model\Entity\Transaction[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class TransactionsController extends AppController
{

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('TransactionCode');
    }
    public function index()
    {
        $this->paginate = [
            'contain' => ['Customers', 'CreatedByUsers', 'ModifiedByUsers'],
        ];
        $transactions = $this->paginate($this->Transactions);


        $tableGreeting = $this->Transactions->greet('Transaction');

        $this->set(compact('transactions', 'tableGreeting'));

        // $this->set(compact('transactions'));
    }

    /**
     * View method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $transaction = $this->Transactions->get($id, [
            'contain' => ['Suppliers', 'CreatedByUsers', 'ModifiedByUsers'],
        ]);

        $this->set(compact('transaction'));
    }

    // langsung kirim aja gausa isi isi
    public function add()
    {
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // unique code
            $transactionDate = $data['transaction_date'];
            $data['code'] = $this->TransactionCode->generateCode('PRC', $transactionDate);

            $transaction = $this->Transactions->patchEntity($transaction, $data);

            $userEmail = $this->request->getSession()->read('Auth.userEmail');

            // Log::write('debug', print_r($transaction, true));

            // Find the user ID based on the email
            $user = $this->Transactions->Users->find('all', [
                'conditions' => ['Users.email' => $userEmail],
            ])->first();

            // Ensure the user was found and set the created_by and modified_by fields
            if ($user) {
                $userId = $user->id;
                $transaction->created_by = $userId;
                $transaction->modified_by = $userId;
            }

            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));

            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            // Log::write('error', json_encode($transaction->getErrors()));

            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $customers = $this->Transactions->Customers->find('list', ['limit' => 200])->all();
        $this->set(compact('transaction', 'customers'));
    }

    public function edit($id = null)
    {
        $transaction = $this->Transactions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());

            // Retrieve the user's email from the session
            $userEmail = $this->request->getSession()->read('Auth.userEmail');

            // Find the user ID based on the email
            $user = $this->Transactions->Users->find('all', [
                'conditions' => ['Users.email' => $userEmail],
            ])->first();

            // Ensure the user was found and set the modified_by field
            if ($user) {
                $userId = $user->id;
                $transaction->modified_by = $userId;
            }

            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $customers = $this->Transactions->Customers->find('list', ['limit' => 200])->all();
        $this->set(compact('transaction', 'customers'));
    }

    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $transaction = $this->Transactions->get($id);
        if ($this->Transactions->delete($transaction)) {
            $this->Flash->success(__('The transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The transaction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
