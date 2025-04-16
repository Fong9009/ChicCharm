<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Auth\DefaultPasswordHasher;

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
        $customer = $this->Customers->get(
            $this->Authentication->getIdentity()->id,
            contain: ['Bookings']
        );


        // Get upcoming bookings with the same structure as customerindex
        $bookingsTable = $this->fetchTable('Bookings');
        $bookings = $bookingsTable->find()
            ->where([
                'customer_id' => $this->Authentication->getIdentity()->id,
                'status' => 'active'
            ])
            ->contain([
                'Customers',
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ])
            ->order([
                'ABS(DATEDIFF(booking_date, CURDATE()))' => 'ASC',
                'booking_date' => 'ASC',
                'start_time' => 'ASC'
            ])
            ->limit(5);

        $this->set(compact('customer', 'bookings'));
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Allow sorting by specific fields
        $this->paginate = [
            'order' => ['created' => 'DESC']
        ];

        $query = $this->Customers->find();

        // Search functionality
        $search = $this->request->getQuery('search');
        if ($search) {
            $query->where([
                'OR' => [
                    'first_name LIKE' => '%' . $search . '%',
                    'last_name LIKE' => '%' . $search . '%',
                    'email LIKE' => '%' . $search . '%',
                ]
            ]);
        }

        // Filter functionality (using the dropdown)
        $filter = $this->request->getQuery('filter');

        // Pagination applies sorting based on request or default
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
            $data = $this->request->getData();

            // Check if email exists in customers table
            $existingCustomer = $this->Customers->find()
                ->where(['email' => $data['email']])
                ->first();

            // Check if email exists in admins table
            $adminsTable = $this->fetchTable('Admins');
            $existingAdmin = $adminsTable->find()
                ->where(['email' => $data['email']])
                ->first();

            // Check if email exists in stylists table
            $stylistsTable = $this->fetchTable('Stylists');
            $existingStylist = $stylistsTable->find()
                ->where(['email' => $data['email']])
                ->first();

            if ($existingCustomer || $existingAdmin || $existingStylist) {
                $this->Flash->error(__('This email is already registered. Please use a different email address.'));
                return $this->redirect(['controller' => 'Customers', 'action' =>  'registration']);
            }

            $customer = $this->Customers->patchEntity($customer, $data);
            $customer->type = 'customer';

            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('Registration successful! Please login with your credentials.'));
                return $this->redirect(['controller' => 'Auth', 'action' =>  'login']);
            }

            // Show specific error messages for each field
            if ($customer->getErrors()) {
                foreach ($customer->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('Registration failed. Please, try again.'));
            }
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
            $data = $this->request->getData();
            $error = 0;

            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $customer->password;
            } else {
                $data['password'] = $password;
            }

            //Profile Picture Upload
            $profile = $this->request->getData('profile_picture');
            if ($profile && $profile->getClientFilename()) {
                //Max Size to prevent massive files from being inserted
                //This is measured in MB so max = 4MB
                $maxSize = 4 * 1024 * 1024;
                if ($profile->getSize() > $maxSize) {
                    $error = 1;
                    $this->Flash->error(__('The profile picture is too big please use something smaller than 4MB.'));
                }

                //Check Filetype
                $allowedFileTypes = ['image/jpeg', 'image/png','image/jpg'];
                if (!in_array($profile->getClientMediaType(), $allowedFileTypes)) {
                    $error = 1;
                    $this->Flash->error(__('The profile picture must be a jpeg/jpg or png format.'));
                }

                //Delete old Image if there is one
                if ($customer->profile_picture != null && $error !== 1) {
                    $oldPath = WWW_ROOT . 'img/profile/' . $customer->profile_picture;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                    $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['profile_picture'] = $filename;
                }
                if ($customer->profile_picture === null && $error !== 1) {
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                    $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['profile_picture'] = $filename;
                }
            } else {
                $data['profile_picture'] = null;
            }

            // Keep existing nonce and nonce_expiry values
            if ($customer->nonce && $customer->nonce_expiry) {
                $data['nonce'] = $customer->nonce;
                $data['nonce_expiry'] = $customer->nonce_expiry;
            }

            // Patch the entity with the data
            $customer = $this->Customers->patchEntity($customer, $data);

            if ($error !== 1) {
                if ($this->Customers->save($customer)) {
                    $this->Flash->success(__('Your profile has been updated.'));
                    // Redirect based on user type
                    if ($user->type === 'admin') {
                        return $this->redirect(['action' => 'index']);
                    } else {
                        return $this->redirect(['action' => 'dashboard']);
                    }
                }
            }

            // Show specific error messages for each field
            if ($customer->getErrors()) {
                foreach ($customer->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The profile could not be updated. Please, try again.'));
            }
        }
        $this->set(compact('customer'));
        // Pass user type to view
        $this->set('userType', $user->type);
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
        $this->request->allowMethod(['post', 'delete']);

        // Get current user
        $currentUser = $this->Authentication->getIdentity();

        // If user is a customer, they cannot delete any account
        if ($currentUser->type === 'customer') {
            $this->Flash->error(__('Customers cannot delete accounts. Please contact an administrator.'));
            return $this->redirect(['action' => 'dashboard']);
        }

        $customer = $this->Customers->get($id);
        if ($this->Customers->delete($customer)) {
            $this->Flash->success(__('The customer has been deleted.'));
        } else {
            $this->Flash->error(__('The customer could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function changePassword()
    {
        $customer = $this->Customers->get($this->Authentication->getIdentity()->id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verify current password
            $hasher = new DefaultPasswordHasher();
            if (!$hasher->check($data['current_password'], $customer->password)) {
                $this->Flash->error(__('Current password is incorrect.'));
                return $this->redirect(['action' => 'changePassword']);
            }

            $customer = $this->Customers->patchEntity($customer, $data, [
                'validate' => 'resetPassword'
            ]);

            if ($this->Customers->save($customer)) {
                $this->Flash->success(__('Your password has been updated successfully.'));
                return $this->redirect(['action' => 'edit', $customer->id]);
            }

            // Show specific error messages
            if ($customer->getErrors()) {
                foreach ($customer->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__($error));
                    }
                }
            } else {
                $this->Flash->error(__('Unable to update your password. Please try again.'));
            }
        }

        $this->set(compact('customer'));
        $this->set('userType', 'customer');
    }

}


