<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenDate;

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
        if ($user && $user->type === 'stylist') {

            return $this->redirect(['controller' => 'Stylists','action' => 'dashboard']);
        }

        if ($user && $user->type === 'guest') {

            return $this->redirect(['controller' => 'Pages','action' => 'display']);
        }

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
        $customer = $this->Customers->get($this->Authentication->getIdentity()->id);
        $bookingsTable = $this->fetchTable('Bookings');

        $today = FrozenDate::today();
        // Get upcoming active bookings (limited to 3)
        $activeBookingsQuery = $bookingsTable->find()
            ->where([
                'customer_id' => $this->Authentication->getIdentity()->id,
                'status IN' => ['active', 'Confirmed - Payment Due', 'Confirmed - Paid'],
                'booking_date >=' => $today,
            ])
            ->contain([
                'Customers',
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'PaymentHistories' => [
                    'fields' => ['id', 'booking_id', 'invoice_pdf', 'payment_date', 'payment_method', 'payment_status'],
                    'sort' => ['PaymentHistories.payment_date' => 'DESC']
                ]
            ])
            ->select([
                'Bookings.id',
                'Bookings.customer_id',
                'Bookings.booking_name',
                'Bookings.booking_date',
                'Bookings.total_cost',
                'Bookings.remaining_cost',
                'Bookings.status',
                'Bookings.refund_due_amount',
            ])
            ->order([
                'ABS(DATEDIFF(booking_date, CURDATE()))' => 'ASC',
                'booking_date' => 'ASC'
            ]);

        $activeBookings = $activeBookingsQuery->all();

        foreach ($activeBookings as $booking) {
            $latestPayment = null;
            if (!empty($booking->payment_histories)) {
                $latestPayment = $booking->payment_histories[0];
            }
            $booking->latest_payment_history = $latestPayment;
        }

        // Get recent past or all cancelled bookings (limited to 3 for dashboard)
        $pastOrCancelledBookings = $bookingsTable->find()
            ->where([
                'customer_id' => $this->Authentication->getIdentity()->id,
                'OR' => [
                    ['Bookings.status' => 'cancelled'], 
                    [
                        'Bookings.status IN' => ['finished', 'Confirmed - Payment Due', 'Confirmed - Paid'], 
                        'Bookings.booking_date <' => $today
                    ]
                ]
            ])
            ->contain([
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'PaymentHistories' => [
                    'fields' => ['id', 'booking_id', 'invoice_pdf', 'payment_date', 'payment_method', 'payment_status'],
                    'sort' => ['PaymentHistories.payment_date' => 'DESC']
                ]
            ])
            ->select([
                'Bookings.id',
                'Bookings.customer_id',
                'Bookings.booking_name',
                'Bookings.booking_date',
                'Bookings.total_cost',
                'Bookings.status',
                'Bookings.refund_due_amount'
            ])
            ->order([
                'Bookings.booking_date' => 'DESC'
            ]);

        // Process latest_payment_history for these bookings as well
        foreach ($pastOrCancelledBookings as $booking) {
            $latestPayment = null;
            if (!empty($booking->payment_histories)) {
                $latestPayment = $booking->payment_histories[0];
            }
            $booking->latest_payment_history = $latestPayment;
        }

        $this->set(compact('customer', 'activeBookings', 'pastOrCancelledBookings'));
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

            //Check if Email is Valid
            $domain = substr(strrchr($data['email'], '@'), 1);
            if (!$domain || !checkdnsrr($domain . '.', 'MX')) {
                $customer->setError('email', ['This is not a valid email']);
            }

            if ($data['email'] !== $customer->email) {
                // Check if email exists in stylists table
                $customerTable = $this->fetchTable('Customers');
                $existingCustomer = $customerTable->find()
                    ->where(['email' => $data['email']])
                    ->first();
            } else {
                $existingCustomer = false;
            }

            // Check if email exists in customers table
            $stylistTable = $this->fetchTable('Stylists');
            $existingStylist = $stylistTable->find()
                ->where(['email' => $data['email']])
                ->first();

            // Check if email exists in admins table
            $adminsTable = $this->fetchTable('Admins');
            $existingAdmin = $adminsTable->find()
                ->where(['email' => $data['email']])
                ->first();

            //Check for existing emails
            if ($existingStylist || $existingCustomer || $existingAdmin) {
                $customer->setError('email', ['This email is already registered. Please use a different one.']);
            }

            if (!preg_match("/^[a-zA-Z' ]+$/", $data['first_name'])) {
                $customer->setError('first_name', ["First name can only be alphabetic or  '"]);
            } elseif (!preg_match("/^[a-zA-Z' ]+$/", $data['last_name'])) {
                $customer->setError('last_name', ["Last name can only be alphabetic or '"]);
            }

            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $customer->password;
            } else {
                $data['password'] = $password;
            }

            // Keep existing nonce and nonce_expiry values
            if ($customer->nonce && $customer->nonce_expiry) {
                $data['nonce'] = $customer->nonce;
                $data['nonce_expiry'] = $customer->nonce_expiry;
            }

            if (empty($customer->getErrors())) {
                $imageResult = $this->replaceImage($id);
                $customer = $this->Customers->patchEntity($customer, $data);
                if (!$imageResult['error']) {
                    $customer->profile_picture = $imageResult['filename'];
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
            }

            // Show specific error messages for each field
            if ($customer->getErrors()) {
                foreach ($customer->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('Could not update the profile. Please check the form and try again.'));
            }

        }
        $this->set(compact('customer'));
        // Pass user type to view
        $this->set('userType', $user->type);
    }

    private function replaceImage(string $id): array
    {
        //Service Image Updater
        $customer = $this->Customers->get($id, contain: []);
        $customerImage = $this->request->getData('profile_picture');
        $data = ['error' => false, 'filename' => null];
        if ($customerImage->getClientFilename() !== '' && $customerImage->getClientFilename() !== null) {
            if ($customerImage && $customerImage->getClientFilename()) {
                //Max Size to prevent massive files from being inserted
                //This is measured in MB so max = 4MB
                $maxSize = 4 * 1024 * 1024;
                if ($customerImage->getSize() > $maxSize) {
                    $data['error'] = true;
                    $this->Flash->error(__('The profile picture is too big please use something smaller than 4MB.'));
                }

                //Check if the file is a real image
                $tmpFile = $customerImage->getStream()->getMetadata('uri');
                if (!getimagesize($tmpFile)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The uploaded file is not a valid image.'));
                }

                //Check Filetype
                $allowedFileTypes = ['image/jpeg', 'image/png','image/jpg'];
                if (!in_array($customerImage->getClientMediaType(), $allowedFileTypes)) {
                    $data['error'] = true;
                    $this->Flash->error(__('The Profile image must be a jpeg/jpg or png format.'));
                }

                //Delete old Image if there is one
                if ($customer->profile_picture != null && !$data['error']) {
                    $oldPath = WWW_ROOT . 'img/profile/' . $customer->profile_picture;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($customerImage->getClientFilename());
                    $customerImage->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['filename'] = $filename;
                }
                if ($customer->profile_picture === null && !$data['error']) {
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($customerImage->getClientFilename());
                    $customerImage->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['filename'] = $filename;
                }
            } else {
                $data['filename'] = null;
            }
        } else {
            $data['filename'] = $customer->profile_picture;
        }

        return $data;
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
        $customer->is_active = false;
        if ($this->Customers->save($customer)) {
            $this->Flash->success(__('The customer has been set to inactive (soft deleted).'));
        } else {
            $this->Flash->error(__('The customer could not be set to inactive. Please, try again.'));
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


