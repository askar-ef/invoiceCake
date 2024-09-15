<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\ORM\TableRegistry;

/**
 * CompanyReport Controller
 *
 * @method \App\Model\Entity\CompanyReport[]|\Cake\Datasource\ResultSetInterface paginate($object = null, array $settings = [])
 */
class CompanyReportController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    // public function index()
    // {
    //     $companyReport = $this->paginate($this->CompanyReport);

    //     $this->set(compact('companyReport'));
    // }

    public function index()
    {
        $this->loadModel('Transactions');
        $this->loadModel('Purchases');

        $transactions = $this->Transactions->find()
            ->contain(['Customers', 'CreatedByUsers', 'ModifiedByUsers'])
            ->all();

        $purchases = $this->Purchases->find()
            ->contain(['Suppliers', 'CreatedByUsers', 'ModifiedByUsers'])
            ->all();

        $this->set(compact('transactions', 'purchases'));
    }

    /**
     * View method
     *
     * @param string|null $id Company Report id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $companyReport = $this->CompanyReport->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('companyReport'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $companyReport = $this->CompanyReport->newEmptyEntity();
        if ($this->request->is('post')) {
            $companyReport = $this->CompanyReport->patchEntity($companyReport, $this->request->getData());
            if ($this->CompanyReport->save($companyReport)) {
                $this->Flash->success(__('The company report has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The company report could not be saved. Please, try again.'));
        }
        $this->set(compact('companyReport'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Company Report id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $companyReport = $this->CompanyReport->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $companyReport = $this->CompanyReport->patchEntity($companyReport, $this->request->getData());
            if ($this->CompanyReport->save($companyReport)) {
                $this->Flash->success(__('The company report has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The company report could not be saved. Please, try again.'));
        }
        $this->set(compact('companyReport'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Company Report id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $companyReport = $this->CompanyReport->get($id);
        if ($this->CompanyReport->delete($companyReport)) {
            $this->Flash->success(__('The company report has been deleted.'));
        } else {
            $this->Flash->error(__('The company report could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
