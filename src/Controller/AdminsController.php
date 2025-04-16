<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Routing\Router;
use Cake\Http\Exception\NotFoundException;
use Cake\Mailer\Mailer;

/**
 * Admins Controller
 *
 * @property \App\Model\Table\AdminsTable $Admins
 */
class AdminsController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Check if user is admin for all actions
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
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

        $query = $this->Admins->find();

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
        $admins = $this->paginate($query);

        $this->set(compact('admins'));
    }

    /**
     * View method
     *
     * @param string|null $id Admin id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $admin = $this->Admins->get($id, contain: []);
        $this->set(compact('admin'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $admin = $this->Admins->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Check if email exists in admins table
            $existingAdmin = $this->Admins->find()
                ->where(['email' => $data['email']])
                ->first();
                
            // Check if email exists in customers table
            $customersTable = $this->fetchTable('Customers');
            $existingCustomer = $customersTable->find()
                ->where(['email' => $data['email']])
                ->first();
                
            // Check if email exists in stylists table
            $stylistsTable = $this->fetchTable('Stylists');
            $existingStylist = $stylistsTable->find()
                ->where(['email' => $data['email']])
                ->first();

            if ($existingAdmin || $existingCustomer || $existingStylist) {
                $this->Flash->error(__('This email is already registered. Please use a different email address.'));
                return;
            }
            
            $data['type'] = 'admin';
            $admin = $this->Admins->patchEntity($admin, $data);
            if ($this->Admins->save($admin)) {
                $this->Flash->success(__('The admin has been saved.'), ['key' => 'admin_notify']);
                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($admin->getErrors()) {
                foreach ($admin->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The admin could not be saved. Please, try again.'));
            }
        }
        $this->set(compact('admin'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Admin id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        // Get current user
        $currentUser = $this->Authentication->getIdentity();

        // Only allow admins to edit their own profile
        if ($currentUser->id != $id) {
            $this->Flash->error('Access denied. You can only edit your own profile.');
            return $this->redirect(['action' => 'index']);
        }

        $admin = $this->Admins->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $error = 0;
            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $admin->password;
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
                if ($admin->profile_picture != null && $error !== 1) {
                    $oldPath = WWW_ROOT . 'img/profile/' . $admin->profile_picture;
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                    $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['profile_picture'] = $filename;
                }
                if ($admin->profile_picture === null && $error !== 1) {
                    //Stores file in directory
                    $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                    $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                    $data['profile_picture'] = $filename;
                }
            } else {
                $data['profile_picture'] = null;
            }
            $data['nonce'] = null;
            // Patch the entity with the data
            $admin = $this->Admins->patchEntity($admin, $data);

            if ($error !== 1) {
                if ($this->Admins->save($admin)) {
                    $this->Flash->success(__('Your profile has been updated.'));
                    return $this->redirect(['action' => 'index']);
                }
            }

            // Show specific error messages for each field
            if ($admin->getErrors()) {
                foreach ($admin->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The profile could not be updated. Please, try again.'));
            }
        }
        $this->set(compact('admin'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Admin id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);

        // Get current user
        $currentUser = $this->Authentication->getIdentity();

        // Prevent deleting own account
        if ($currentUser->id == $id) {
            $this->Flash->error(__('You cannot delete your own account.'));
            return $this->redirect(['action' => 'index']);
        }

        $admin = $this->Admins->get($id);
        if ($this->Admins->delete($admin)) {
            $this->Flash->success(__('The admin has been deleted.'));
        } else {
            $this->Flash->error(__('The admin could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function dashboard(){
        //Provides Admin Name
        $admin = $this->Authentication->getIdentity();
        $adminName = $admin ? $admin->first_name : 'User';

        //Provides Customer Count
        $customersTable = $this->fetchTable('Customers');
        $custCount = $customersTable->find()->count();

        //Provides Admin Count
        $adminsTable = $this->fetchTable('Admins');
        $adminCount = $adminsTable->find()->count();

        //Provides Contacts Count
        $contactsTable = $this->fetchTable('Contacts');
        $contactCount = $contactsTable->find()->count();

        //Provides Stylists Count
        $stylistsTable = $this->fetchTable('Stylists');
        $stylistCount = $stylistsTable->find()->count();

        //Provides Booking Count
        $bookingsTable = $this->fetchTable('Bookings');
        $bookingCount = $bookingsTable->find()->count();

        //Provides Service Count
        $servicesTable = $this->fetchTable('Services');
        $serviceCount = $servicesTable->find()->count();

        $this->set(compact('custCount', 'adminName', 'adminCount', 'contactCount', 'bookingCount', 'serviceCount', 'stylistCount'));
    }

    public function profile($id = null)
    {
        $user = $this->Authentication->getIdentity();
        //Check if the logged-in admin's ID matches the ID in the URL
        if ($user->id != $id) {
            //Redirect to their own profile
            return $this->redirect(['controller' => 'Admins', 'action' => 'profile', $user->id]);
        }
        // If the IDs match, fetch the admin's profile
        $admin = $this->Admins->get($id);
        // Set variables to pass to the view
        $this->set(compact('admin'));
    }

    public function changePassword()
    {
        $admin = $this->Admins->get($this->Authentication->getIdentity()->id);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Verify current password
            $hasher = new DefaultPasswordHasher();
            if (!$hasher->check($data['current_password'], $admin->password)) {
                $this->Flash->error(__('Current password is incorrect.'));
                return $this->redirect(['action' => 'changePassword']);
            }

            $admin = $this->Admins->patchEntity($admin, $data, [
                'validate' => 'resetPassword'
            ]);

            if ($this->Admins->save($admin)) {
                $this->Flash->success(__('Your password has been updated successfully.'));
                return $this->redirect(['action' => 'edit', $admin->id]);
            }

            // Show specific error messages
            if ($admin->getErrors()) {
                foreach ($admin->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__($error));
                    }
                }
            } else {
                $this->Flash->error(__('Unable to update your password. Please try again.'));
            }
        }

        $this->set(compact('admin'));
        $this->set('userType', 'admin');
    }
}

