<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Customers Controller
 *
 * @property \App\Model\Table\CustomersTable $Customers
 */
class CustomersController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['registration']);
    }

    /**
     * BeforeFilter method
     * This method is called before every action in the controller
     *
     * @param \Cake\Event\EventInterface $event The event object
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);

        $user = $this->request->getAttribute('identity');
        $currentAction = $this->request->getParam('action');

        $restrictedActions = ['index', 'view'];

        if ($user && $user->type === 'customer' && in_array($currentAction, $restrictedActions)) {
            $this->Flash->error('Access denied. You are not authorized to view this page.');

            // Only redirect if we're not already at dashboard to prevent redirect loop
            if ($currentAction !== 'dashboard') {
                return $this->redirect(['action' => 'dashboard']);
            }
        }
    }


    /**
     * Dashboard method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function dashboard()
    {
        $customerId = $this->Authentication->getIdentity()->getIdentifier();

        $customer = $this->Customers->get($customerId);
        $this->set(compact('customer'));

        // Use the default layout which includes the navigation
        $this->viewBuilder()->setLayout('default');
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Customers->find();
        $customers = $this->paginate($query);

        $this->set(compact('customers'));
    }

    /**
     * View method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $customer = $this->Customers->get($id, contain: []);
        $this->set(compact('customer'));
    }

    /**
     * Registration method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful registration, renders view otherwise.
     */
    public function registration()
    {
        $this->viewBuilder()->setLayout('login');

        $customer = $this->Customers->newEmptyEntity();
        if ($this->request->is('post')) {
            $customer = $this->Customers->patchEntity($customer, $this->request->getData());
            $customer->type = 'customer';

            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('Registration successful! Please login with your credentials.'));
                return $this->redirect(['controller' => 'Auth', 'action' =>  'login']);
            }
            $this->Flash->error(__('Registration failed. Please, try again.'));
        }
        $this->set(compact('customer'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Authentication->getIdentity();

        // Only admins can edit profiles
        if ($user->type !== 'admin' && $user->id != $id) {
            $this->Flash->error('Access denied. You can only edit your own profile.');
            return $this->redirect(['action' => 'dashboard']);
        }

        $customer = $this->Customers->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $customer = $this->Customers->patchEntity($customer, $this->request->getData());

            //Profile Picture Upload
            $profile = $this->request->getData('profile_picture');
            if($profile && $profile->getClientFilename()) {
                //Max Size to prevent massive files from being inserted
                //This is measured in MB so max = 2MB
                $maxSize = 2 * 1024 * 1024;
                if($profile->getSize() > $maxSize) {
                    $this->Flash->error(__('The profile picture is too big please use something smaller than 2MB.'));
                }

                //Check Filetype
                $allowedFileTypes = ['image/jpeg', 'image/png','image/jpg'];
                if(!in_array($profile->getClientMediaType(), $allowedFileTypes)) {
                    $this->Flash->error(__('The profile picture must be a jpeg/jpg or png format.'));
                }

                //Delete old Image if there is one
                if($customer->profile_picture) {
                    $oldpath = WWW_ROOT . 'img/profile' . $customer->profile_picture;
                    if(file_exists($oldpath)) {
                        unlink($oldpath);
                    }
                }

                //Stores file in directory
                $filename = rand(10000, 99999) . '_' . $profile->getClientFilename();
                $profile->moveTo(WWW_ROOT.'img/profile/'.$filename);
                $data['profile_picture'] = $filename;
            }

            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('Your profile has been updated.'));

                return $this->redirect(['action' => 'dashboard']);
            }
            $this->Flash->error(__('The profile could not be updated. Please, try again.'));
        }
        $this->set(compact('customer'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Customer id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $user = $this->Authentication->getIdentity();

        // Only admins can delete accounts
        if ($user->type !== 'admin') {
            $this->Flash->error('Access denied. Only administrators can delete accounts.');
            return $this->redirect(['action' => 'dashboard']);
        }

        $this->request->allowMethod(['post', 'delete']);
        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Flash->success(__('The customer has been deleted.'));
        } else {
            $this->Flash->error(__('The customer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}

