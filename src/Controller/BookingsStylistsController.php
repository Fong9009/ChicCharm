<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * BookingsStylists Controller
 *
 * @property \App\Model\Table\BookingsStylistsTable $BookingsStylists
 */
class BookingsStylistsController extends AppController
{
    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        $query = $this->BookingsStylists->find()
            ->contain(['Bookings', 'Stylists']);
        $bookingsStylists = $this->paginate($query);

        $this->set(compact('bookingsStylists'));
    }

    /**
     * View method
     *
     * @param string|null $id Bookings Stylist id.
     * @return \Cake\Http\Response|null|void Renders view
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $bookingsStylist = $this->BookingsStylists->get($id, contain: ['Bookings', 'Stylists']);
        $this->set(compact('bookingsStylist'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $bookingsStylist = $this->BookingsStylists->newEmptyEntity();
        if ($this->request->is('post')) {
            $bookingsStylist = $this->BookingsStylists->patchEntity($bookingsStylist, $this->request->getData());
            if ($this->BookingsStylists->save($bookingsStylist)) {
                $this->Flash->success(__('The bookings stylist has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bookings stylist could not be saved. Please, try again.'));
        }
        $bookings = $this->BookingsStylists->Bookings->find('list', limit: 200)->all();
        $stylists = $this->BookingsStylists->Stylists->find('list', limit: 200)->all();
        $this->set(compact('bookingsStylist', 'bookings', 'stylists'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Bookings Stylist id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $bookingsStylist = $this->BookingsStylists->get($id, contain: []);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $bookingsStylist = $this->BookingsStylists->patchEntity($bookingsStylist, $this->request->getData());
            if ($this->BookingsStylists->save($bookingsStylist)) {
                $this->Flash->success(__('The bookings stylist has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The bookings stylist could not be saved. Please, try again.'));
        }
        $bookings = $this->BookingsStylists->Bookings->find('list', limit: 200)->all();
        $stylists = $this->BookingsStylists->Stylists->find('list', limit: 200)->all();
        $this->set(compact('bookingsStylist', 'bookings', 'stylists'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Bookings Stylist id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $bookingsStylist = $this->BookingsStylists->get($id);
        if ($this->BookingsStylists->delete($bookingsStylist)) {
            $this->Flash->success(__('The bookings stylist has been deleted.'));
        } else {
            $this->Flash->error(__('The bookings stylist could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
