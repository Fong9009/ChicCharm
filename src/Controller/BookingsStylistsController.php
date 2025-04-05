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

    //This function is to make sure that we can check that a stylist is available
    public function customerstylistadd($booking_id = null)
    {

        // Check if the booking ID is provided
        if (!$booking_id) {
            $this->Flash->error(__('Invalid booking missing booking ID'));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'customerindex']);
        }

        //This is to obtain the booking ID's date of booking
        $booking = $this->BookingsStylists->Bookings->get($booking_id);
        $bookingDateOnly = $booking->booking_date->format('Y-m-d'); // Format it to 'Y-m-d' for comparison

        //Initialisation of the query Variable
        $query = null;

        if ($this->request->is(['post'])) {
            $serviceId = $this->request->getData('service_id');
            $startTime = $this->request->getData('start_time');
            $endTime = $this->request->getData('end_time');


            //Stylist filter based on selected service
            //To ensure that we only get stylists that the client want
            $query = $this->BookingsStylists->Stylists->find()
                ->select([
                    'Stylists.id',
                    'Stylists.first_name',
                    'Stylists.last_name',
                    'Services.service_name',
                ])
                ->leftJoinWith('Services')
                ->where(['Services.id' => $serviceId])
                ->distinct(['Stylists.id']);

            //Subquery to remove stylists who have conflicting bookings in the case of time and date
            //Ensures that the client can select their time
            $query->notMatching('BookingsStylists', function ($q) use ($bookingDateOnly, $startTime, $endTime) {
                return $q->where([
                    'BookingsStylists.stylist_date' => $bookingDateOnly, // Ensure the booking date matches with booking
                    'BookingsStylists.start_time <' => $endTime, // Existing booking ends after new start time
                    'BookingsStylists.end_time >' => $startTime,  // Existing booking starts before new end time
                    'BookingsStylists.start_time IS NOT NULL',
                    'BookingsStylists.end_time IS NOT NULL',
                ]);
            });
        }
        $bookingsStylist = $this->BookingsStylists->newEmptyEntity();

        //If nothing was obtained such as no available contractor
        if ($query === null) {
            $query = null;
        }

        // Paginate the results if query is valid
        $filterStylists = $this->paginate($query);
        // Fetch the list of services and bookings
        $services = $this->BookingsStylists->Stylists->Services->find('list')->toArray();
        $bookings = $this->BookingsStylists->Bookings->find('list', limit: 200)->all();
        // Pass the data to the view
        $this->set(compact('bookingsStylist', 'bookings', 'filterStylists', 'services', 'booking_id'));
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
