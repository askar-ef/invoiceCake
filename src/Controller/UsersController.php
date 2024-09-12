<?php

declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Log\Log;

class UsersController extends AppController
{

    public function beforeFilter(EventInterface $event)
    {
        // $this->Authentication->addUnauthenticatedActions(['login', 'add']);
        $this->Authentication->addUnauthenticatedActions(['login']);
        parent::beforeFilter($event);
    }

    public function login()
    {
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $user = $result->getData();

            // // Tambahkan log untuk memeriksa data pengguna
            // Log::write('info', 'Authenticated user data: ' . json_encode($user));

            // Ambil objek session dan simpan username/email ke dalam sesi
            $session = $this->getRequest()->getSession();
            $session->write('Auth.userEmail', $user->email);

            // // Tambahkan log untuk memeriksa apakah sesi berhasil dibuat
            // Log::write('info', 'Session created for email: ' . $user->email);

            // // Baca kembali seluruh nilai yang ada di sesi untuk verifikasi
            // $sessionData = $session->read();
            // Log::write('info', 'Complete session data: ' . json_encode($sessionData));

            $redirect = $this->request->getQuery('redirect', [
                'controller' => 'Customers',
                'action' => 'index',
            ]);

            return $this->redirect($redirect);
        }

        if ($this->request->is('post') && !$result->isValid()) {
            $this->Flash->error(__('Invalid username or password'));
        }
    }


    public function logout()
    {
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            $this->Authentication->logout();
            $session = $this->getRequest()->getSession();
            $session->delete('Auth.userEmail');

            return $this->redirect(['controller' => 'Users', 'action' => 'login']);
        }
    }
    public function index()
    {
        $users = $this->paginate($this->Users);

        $session = $this->getRequest()->getSession();
        $sessionUserName = $session->read('Auth.userEmail');

        if ($sessionUserName) {
            $this->Flash->success('Session username: ' . h($sessionUserName));
        } else {
            $this->Flash->info('No session username found.');
        }

        $this->set(compact('users'));
    }



    public function view($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);

        $this->set(compact('user'));
    }


    public function add()
    {
        $user = $this->Users->newEmptyEntity();
        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }


    public function edit($id = null)
    {
        $user = $this->Users->get($id, [
            'contain' => [],
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData());
            if ($this->Users->save($user)) {
                $this->Flash->success(__('The user has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The user could not be saved. Please, try again.'));
        }
        $this->set(compact('user'));
    }


    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $user = $this->Users->get($id);
        if ($this->Users->delete($user)) {
            $this->Flash->success(__('The user has been deleted.'));
        } else {
            $this->Flash->error(__('The user could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
