<?php

declare(strict_types=1);

namespace App\Controller;

class ReportTransactionsController extends AppController
{
    public function index()
    {
        if ($this->request->is('post')) {
            $company['name'] = $this->getRequest()->getSession()->read('Auth.company_name');
            $company['address'] = $this->getRequest()->getSession()->read('Auth.company_address');
            $table = $this->fetchTable('Transactions.Purchases');
            $results = $table->find()
                ->where(function ($exp, $q) {
                    return $exp->between('purchase_date', $this->request->getData('start'), $this->request->getData('end'));
                })
                ->contain(['PurchaseDetails'])
                ->order(['purchase_code' => 'asc'])
                ->all();
            if (empty($results)) {
                $this->Flash->set(__('Data tidak tersedia.'));
                return $this->redirect(['action' => 'index']);
            }
            switch ($this->request->getData('type')) {
                case 'html':
                    $file = 'html';
                    break;
                case 'excel':
                    $file = 'excel';
                    break;

                default:
                    $file = 'html';
                    break;
            }
            $periode = date("d F Y", strtotime($this->request->getData('start'))) . ' - ' . date("d F Y", strtotime($this->request->getData('end')));
            $this->set(compact('results', 'company', 'periode'));
            $this->render($file);
        }
    }
    /**
     * View method
     *
     * @param string|null $id Report Transaction id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $reportTransaction = $this->ReportTransactions->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('reportTransaction'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $reportTransaction = $this->ReportTransactions->newEmptyEntity();
        if ($this->request->is('post')) {
            $reportTransaction = $this->ReportTransactions->patchEntity($reportTransaction, $this->request->getData());
            if ($this->ReportTransactions->save($reportTransaction)) {
                $this->Flash->success(__('The report transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The report transaction could not be saved. Please, try again.'));
        }
        $this->set(compact('reportTransaction'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Report Transaction id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $reportTransaction = $this->ReportTransactions->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $reportTransaction = $this->ReportTransactions->patchEntity($reportTransaction, $this->request->getData());
            if ($this->ReportTransactions->save($reportTransaction)) {
                $this->Flash->success(__('The report transaction has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The report transaction could not be saved. Please, try again.'));
        }
        $this->set(compact('reportTransaction'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Report Transaction id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $reportTransaction = $this->ReportTransactions->get($id);
        if ($this->ReportTransactions->delete($reportTransaction)) {
            $this->Flash->success(__('The report transaction has been deleted.'));
        } else {
            $this->Flash->error(__('The report transaction could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
