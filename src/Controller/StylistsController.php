<?php
declare(strict_types=1);

namespace App\Controller;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\Event\EventInterface;
/**
 * Stylists Controller
 *
 * @property \App\Model\Table\StylistsTable $Stylists
 */
class StylistsController extends AppController
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
        if (!$user) {
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        } elseif ($user->type === 'customer') {
            return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Stylists->find();
        $stylists = $this->paginate($query);

        $this->set(compact('stylists'));
    }

    /**
     * View method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $stylist = $this->Stylists->get($id, contain: ['Bookings', 'Services']);
        $this->set(compact('stylist'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $stylist = $this->Stylists->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Check if email exists in stylists table
            $existingStylist = $this->Stylists->find()
                ->where(['email' => $data['email']])
                ->first();

            // Check if email exists in customers table
            $customersTable = $this->fetchTable('Customers');
            $existingCustomer = $customersTable->find()
                ->where(['email' => $data['email']])
                ->first();

            // Check if email exists in admins table
            $adminsTable = $this->fetchTable('Admins');
            $existingAdmin = $adminsTable->find()
                ->where(['email' => $data['email']])
                ->first();

            if ($existingStylist || $existingCustomer || $existingAdmin) {
                $this->Flash->error(__('This email is already registered. Please use a different email address.'));

                return;
            }

            $data['type'] = 'stylist';
            $stylist = $this->Stylists->patchEntity($stylist, $data);
            if ($this->Stylists->save($stylist)) {
                $this->Flash->success(__('The stylist has been saved.'));

                return $this->redirect(['action' => 'index']);
            }

            // Show specific error messages for each field
            if ($stylist->getErrors()) {
                foreach ($stylist->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The stylist could not be saved. Please, try again.'));
            }
        }
        $bookings = $this->Stylists->Bookings->find('list', limit: 200)->all();
        $services = $this->Stylists->Services->find('list', limit: 200)->all();
        $this->set(compact('stylist', 'bookings', 'services'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $user = $this->Authentication->getIdentity();
        //Only admins can edit profiles
        if ($user->type !== 'admin' && $user->id != $id) {
            $this->Flash->error('Access denied. You can only edit your own profile.');

            return $this->redirect(['action' => 'dashboard']);
        }

        $stylist = $this->Stylists->get($id, contain: ['Services']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $error = 0;

            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $stylist->password;
            } else {
                $data['password'] = $password;
            }


            //Profile Picture Upload
            $profile = $this->request->getData('profile_picture');
            if ($profile->getClientFilename() !== '' && $profile->getClientFilename() !== null) {
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
                    if ($stylist->profile_picture != null && $error !== 1) {
                        $oldPath = WWW_ROOT . 'img/profile/' . $stylist->profile_picture;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                        //Stores file in directory
                        $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                        $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                        $data['profile_picture'] = $filename;
                    }
                    if ($stylist->profile_picture === null && $error !== 1) {
                        //Stores file in directory
                        $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                        $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                        $data['profile_picture'] = $filename;
                    }
                } else {
                    $data['profile_picture'] = null;
                }
            } else {
                $data['profile_picture'] = $stylist->profile_picture;
            }

            // Keep existing nonce and nonce_expiry values
            if ($stylist->nonce && $stylist->nonce_expiry) {
                $data['nonce'] = $stylist->nonce;
                $data['nonce_expiry'] = $stylist->nonce_expiry;
            }

            $stylist = $this->Stylists->patchEntity($stylist, $data);
            if ($error !== 1) {
                if ($this->Stylists->save($stylist)) {
                    $this->Flash->success(__('The stylist has been saved.'));
                    // Redirect based on user type
                    if ($user->type === 'admin') {
                        return $this->redirect(['action' => 'index']);
                    } else {
                        return $this->redirect(['action' => 'dashboard']);
                    }
                }
            }

            // Show specific error messages for each field
            if ($stylist->getErrors()) {
                foreach ($stylist->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The stylist could not be saved. Please, try again.'));
            }
        }
        $bookings = $this->Stylists->Bookings->find('list', limit: 200)->all();
        $services = $this->Stylists->Services->find('list', limit: 200)->all();
        $this->set(compact('stylist', 'bookings', 'services'));
        $this->set('userType', $user->type);
    }

    public function stylistedit($id = null) {
        $user = $this->Authentication->getIdentity();
        //Only admins can edit profiles
        if ($user->type !== 'admin' && $user->id != $id) {
            $this->Flash->error('Access denied. You can only edit your own profile.');

            return $this->redirect(['action' => 'dashboard']);
        }

        $stylist = $this->Stylists->get($id, contain: ['Services']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $error = 0;

            $password = $this->request->getData('password');
            if ($password == null || $password == '') {
                $data['password'] = $stylist->password;
            } else {
                $data['password'] = $password;
            }


            //Profile Picture Upload
            $profile = $this->request->getData('profile_picture');
            if ($profile->getClientFilename() !== '' && $profile->getClientFilename() !== null) {
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
                    if ($stylist->profile_picture != null && $error !== 1) {
                        $oldPath = WWW_ROOT . 'img/profile/' . $stylist->profile_picture;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                        //Stores file in directory
                        $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                        $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                        $data['profile_picture'] = $filename;
                    }
                    if ($stylist->profile_picture === null && $error !== 1) {
                        //Stores file in directory
                        $filename = rand(10000, 99999) . '_' . strtolower($profile->getClientFilename());
                        $profile->moveTo(WWW_ROOT . 'img/profile/' . $filename);
                        $data['profile_picture'] = $filename;
                    }
                } else {
                    $data['profile_picture'] = null;
                }
            } else {
                $data['profile_picture'] = $stylist->profile_picture;
            }

            // Keep existing nonce and nonce_expiry values
            if ($stylist->nonce && $stylist->nonce_expiry) {
                $data['nonce'] = $stylist->nonce;
                $data['nonce_expiry'] = $stylist->nonce_expiry;
            }

            $stylist = $this->Stylists->patchEntity($stylist, $data);
            if ($error !== 1) {
                if ($this->Stylists->save($stylist)) {
                    $this->Flash->success(__('The stylist has been saved.'));
                    // Redirect based on user type
                    if ($user->type === 'admin') {
                        return $this->redirect(['action' => 'index']);
                    } else {
                        return $this->redirect(['action' => 'dashboard']);
                    }
                }
            }

            // Show specific error messages for each field
            if ($stylist->getErrors()) {
                foreach ($stylist->getErrors() as $field => $errors) {
                    foreach ($errors as $error) {
                        $this->Flash->error(__("{0}: {1}", ucfirst($field), $error));
                    }
                }
            } else {
                $this->Flash->error(__('The stylist could not be saved. Please, try again.'));
            }
        }
        $bookings = $this->Stylists->Bookings->find('list', limit: 200)->all();
        $services = $this->Stylists->Services->find('list', limit: 200)->all();
        $this->set(compact('stylist', 'bookings', 'services'));
        $this->set('userType', $user->type);
    }

    /**
     * Delete method
     *
     * @param string|null $id Stylist id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $stylist = $this->Stylists->get($id, [
            'contain' => ['BookingsStylists' => function ($q) {
                return $q->innerJoinWith('Bookings', function ($q) {
                    return $q->where(['Bookings.status' => 'active']);
                });
            }],
        ]);

        // Check if stylist has any active bookings
        if (!empty($stylist->bookings_stylists)) {
            $this->Flash->error(__('Cannot delete stylist as they have active bookings.'));

            return $this->redirect(['action' => 'index']);
        }

        if ($this->Stylists->delete($stylist)) {
            $this->Flash->success(__('The stylist has been deleted.'));
        } else {
            $this->Flash->error(__('The stylist could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * Dashboard method for stylist
     *
     * @return void
     */
    public function dashboard(): void
    {
        $stylist = $this->Stylists->get($this->Authentication->getIdentity()->id);

        //Bookings that have the selected Stylist
        $bookingsTable = $this->fetchTable('Bookings');
        $activeBookings = $bookingsTable->find()
            ->contain([
                'BookingsStylists',
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id','first_name','last_name'],
                    ],
                ],
            ])
            ->matching('BookingsStylists', function ($q) use ($stylist) {
                return $q->where(['BookingsStylists.stylist_id' => $stylist->id]);
            })
            ->where(['Bookings.status' => 'active'])
            ->orderBy(['Bookings.booking_date' => 'DESC'])
            ->limit(3);

        // Get recent cancelled bookings (limited to 3)
        $finishedBookings = $bookingsTable->find()
            ->contain([
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id','first_name','last_name'],
                    ],
                ],
            ])
            ->matching('BookingsServices', function ($q) use ($stylist) {
                return $q->where(['BookingsServices.stylist_id' => $stylist->id]);
            })
            ->where(['Bookings.status IN' => ['finished', 'cancelled']])
            ->orderBy(['Bookings.booking_date' => 'DESC'])
            ->limit(3);
        $this->set(compact('stylist', 'activeBookings', 'finishedBookings'));
    }

    public function stylistOverview()
    {
        $this->paginate = [
            'limit' => 12, // Show 6 services per page
        ];

        // Search functionality
        $query = $this->Stylists->find();
        $search = $this->request->getQuery('search');
        if ($search) {
            $query->where([
                'OR' => [
                    'first_name LIKE' => '%' . $search . '%',
                    'last_name LIKE' => '%' . $search . '%',
                ],
            ]);
        }
        $stylists = $this->paginate($query);
        $this->set(compact('stylists'));

    }
}
