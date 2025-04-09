<?php
declare(strict_types=1);

namespace App\Controller;

use Cake\Event\EventInterface;

/**
 * Bookings Controller
 *
 * @property \App\Model\Table\BookingsTable $Bookings
 */
class BookingsController extends AppController
{
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Get the current action
        $currentAction = $this->request->getParam('action');
        
        // Get the logged-in user's identity
        $identity = $this->request->getAttribute('identity');
        
        // List of customer-specific actions
        $customerActions = ['customerindex', 'customerview', 'customerbooking', 'customerdelete'];
        
        // If it's a customer-specific action, ensure the user is logged in
        if (in_array($currentAction, $customerActions)) {
            if (!$identity) {
                $this->Flash->error(__('Please log in to access this page.'));
                return $this->redirect(['controller' => 'Customers', 'action' => 'login']);
            }
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->Bookings->find()
            ->contain(['Customers']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }

    public function customerindex()
    {
        $customerId = $this->request->getAttribute('identity')->id;
        $query = $this->Bookings->find()
            ->contain(['Customers'])
            ->where(['Bookings.customer_id' => $customerId])
            ->order(['Bookings.booking_date' => 'DESC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }


    /**
     * View method
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $booking = $this->Bookings->get($id, contain: ['Customers', 'Stylists']);
        $this->set(compact('booking'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $booking = $this->Bookings->patchEntity($booking, $this->request->getData());
            if ($this->Bookings->save($booking)) {
                $this->Flash->success(__('The booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $this->set(compact('booking', 'customers', 'stylists'));
    }
    /**
     * Edit method
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $booking = $this->Bookings->get($id, contain: ['Stylists']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $booking = $this->Bookings->patchEntity($booking, $this->request->getData());
            if ($this->Bookings->save($booking)) {
                $this->Flash->success(__('The booking has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $this->set(compact('booking', 'customers', 'stylists'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $booking = $this->Bookings->get($id);
        if ($this->Bookings->delete($booking)) {
            $this->Flash->success(__('The booking has been deleted.'));
        } else {
            $this->Flash->error(__('The booking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function customerdelete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        
        $customerId = $this->request->getAttribute('identity')->id;
        $booking = $this->Bookings->get($id);
        
        // Check if the booking belongs to the logged-in customer
        if ($booking->customer_id !== $customerId) {
            $this->Flash->error(__('You are not authorized to delete this booking.'));
            return $this->redirect(['action' => 'customerindex']);
        }
        
        if ($this->Bookings->delete($booking)) {
            $this->Flash->success(__('The booking has been deleted.'));
        } else {
            $this->Flash->error(__('The booking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'customerindex']);
    }

    public function customerbooking()
    {
        $loggedUser = $this->request->getAttribute('identity');

        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $booking->customer_id = $loggedUser->id;
            $booking = $this->Bookings->patchEntity($booking, $this->request->getData());
            if ($this->Bookings->save($booking)) {
                $this->Flash->success(__('The booking has been saved.'));
                return $this->redirect(['action' => 'customerview', $booking->id]);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $this->set(compact('booking'));
    }

    /**
     * View method
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function customerview($id = null)
    {
        $customerId = $this->request->getAttribute('identity')->id;
        
        $booking = $this->Bookings->get($id, [
            'contain' => ['Customers', 'Stylists'],
        ]);
        
        // Check if the booking belongs to the logged-in customer
        if ($booking->customer_id !== $customerId) {
            $this->Flash->error(__('You are not authorized to view this booking.'));
            return $this->redirect(['action' => 'customerindex']);
        }
        
        $this->set(compact('booking'));
    }
}
