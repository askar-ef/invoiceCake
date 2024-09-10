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
            'contain' => ['Customers'],
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
            'contain' => ['Customers'],
        ]);

        $this->set(compact('transaction'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    // ngisi dulu kolomnya
    // public function add()
    // {
    //     $transaction = $this->Transactions->newEmptyEntity();
    //     if ($this->request->is('post')) {
    //         // Generate the transaction code
    //         $transactionDate = $this->request->getData('transaction_date');
    //         $transaction->code = $this->TransactionCode->generateCode('PRC', $transactionDate);

    //         $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());

    //         // Log the transaction data before saving
    //         Log::write('debug', print_r($transaction, true));

    //         if ($this->Transactions->save($transaction)) {
    //             $this->Flash->success(__('The transaction has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }

    //         // Log any errors if the save failed
    //         Log::write('error', json_encode($transaction->getErrors()));

    //         $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
    //     }
    //     $customers = $this->Transactions->Customers->find('list', ['limit' => 200])->all();
    //     $this->set(compact('transaction', 'customers'));
    // }

    // langsung kirim aja gausa isi isi
    public function add()
    {
        $transaction = $this->Transactions->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            $transactionDate = $data['transaction_date'];
            $data['code'] = $this->TransactionCode->generateCode('PRC', $transactionDate);

            $transaction = $this->Transactions->patchEntity($transaction, $data);

            Log::write('debug', print_r($transaction, true));

            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            Log::write('error', json_encode($transaction->getErrors()));

            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $customers = $this->Transactions->Customers->find('list', ['limit' => 200])->all();
        $this->set(compact('transaction', 'customers'));
    }


    /**
     * Edit method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $transaction = $this->Transactions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $transaction = $this->Transactions->patchEntity($transaction, $this->request->getData());
            if ($this->Transactions->save($transaction)) {
                $this->Flash->success(__('The transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The transaction could not be saved. Please, try again.'));
        }
        $customers = $this->Transactions->Customers->find('list', ['limit' => 200])->all();
        $this->set(compact('transaction', 'customers'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Transaction id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
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
