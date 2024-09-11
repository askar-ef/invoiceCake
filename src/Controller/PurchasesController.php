<?php

declare(strict_types=1);

namespace App\Controller;

/**
 * Purchases Controller
 *
 * @property \App\Model\Table\PurchasesTable $Purchases
 * @method \App\Model\Entity\Purchase[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class PurchasesController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Suppliers', 'CreatedByUsers', 'ModifiedByUsers'],
        ];
        $purchases = $this->paginate($this->Purchases);

        $this->set(compact('purchases'));
    }

    public function view($id = null)
    {
        $purchase = $this->Purchases->get($id, [
            'contain' => ['Suppliers'],
        ]);

        $this->set(compact('purchase'));
    }

    public function add()
    {
        $purchase = $this->Purchases->newEmptyEntity();
        if ($this->request->is('post')) {
            $purchase = $this->Purchases->patchEntity($purchase, $this->request->getData());

            $userEmail = $this->request->getSession()->read('Auth.userEmail');

            // Find the user ID based on the email
            $user = $this->Purchases->Users->find('all', [
                'conditions' => ['Users.email' => $userEmail],
            ])->first();

            // Ensure the user was found and set the created_by and modified_by fields
            if ($user) {
                $userId = $user->id;
                $purchase->created_by = $userId;
                $purchase->modified_by = $userId;
            }

            if ($this->Purchases->save($purchase)) {
                $this->Flash->success(__('The purchase has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase could not be saved. Please, try again.'));
        }


        $suppliers = $this->Purchases->Suppliers->find('list', ['limit' => 200]);
        $this->set(compact('purchase', 'suppliers'));
    }

    public function edit($id = null)
    {
        $purchase = $this->Purchases->get($id, [
            'contain' => [],
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $purchase = $this->Purchases->patchEntity($purchase, $this->request->getData());

            // Retrieve the user's email from the session
            $userEmail = $this->request->getSession()->read('Auth.userEmail');

            // Find the user ID based on the email
            $user = $this->Purchases->Users->find('all', [
                'conditions' => ['Users.email' => $userEmail],
            ])->first();

            // Ensure the user was found and set the modified_by field
            if ($user) {
                $userId = $user->id;
                $purchase->modified_by = $userId;
            }

            if ($this->Purchases->save($purchase)) {
                $this->Flash->success(__('The purchase has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The purchase could not be saved. Please, try again.'));
        }

        $suppliers = $this->Purchases->Suppliers->find('list', ['limit' => 200]);
        $this->set(compact('purchase', 'suppliers'));
    }


    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    // public function add()
    // {
    //     $purchase = $this->Purchases->newEmptyEntity();
    //     if ($this->request->is('post')) {
    //         $purchase = $this->Purchases->patchEntity($purchase, $this->request->getData());
    //         if ($this->Purchases->save($purchase)) {
    //             $this->Flash->success(__('The purchase has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The purchase could not be saved. Please, try again.'));
    //     }
    //     $suppliers = $this->Purchases->Suppliers->find('list', ['limit' => 200])->all();
    //     $this->set(compact('purchase', 'suppliers'));
    // }

    /**
     * Edit method
     *
     * @param string|null $id Purchase id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    // public function edit($id = null)
    // {
    //     $purchase = $this->Purchases->get($id, [
    //         'contain' => [],
    //     ]);
    //     if ($this->request->is(['patch', 'post', 'put'])) {
    //         $purchase = $this->Purchases->patchEntity($purchase, $this->request->getData());
    //         if ($this->Purchases->save($purchase)) {
    //             $this->Flash->success(__('The purchase has been saved.'));

    //             return $this->redirect(['action' => 'index']);
    //         }
    //         $this->Flash->error(__('The purchase could not be saved. Please, try again.'));
    //     }
    //     $suppliers = $this->Purchases->Suppliers->find('list', ['limit' => 200])->all();
    //     $this->set(compact('purchase', 'suppliers'));
    // }

    /**
     * Delete method
     *
     * @param string|null $id Purchase id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $purchase = $this->Purchases->get($id);
        if ($this->Purchases->delete($purchase)) {
            $this->Flash->success(__('The purchase has been deleted.'));
        } else {
            $this->Flash->error(__('The purchase could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
