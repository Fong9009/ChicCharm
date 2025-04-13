<?php
declare(strict_types=1);

namespace App\Controller;
use Cake\I18n\DateTime;

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

    public function customerstylistadd($booking_id = null)
    {

        //Check if the booking ID is provided
        if (!$booking_id) {
            $this->Flash->error(__('Invalid booking missing booking ID'));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'customerindex']);
        }

        //This is to obtain the booking ID's date of booking
        $booking = $this->BookingsStylists->Bookings->get($booking_id);
        $bookingDateOnly = $booking->booking_date->format('Y-m-d'); // Format it to 'Y-m-d' for comparison

        //Initialisation of the query Variable
        $query = null;

        if ($this->request->is(['post']) && $this->request->getData('filter') === '1') {
            $serviceId = $this->request->getData('service_id');
            $startTime = $this->request->getData('start_time');
            $endTime = $this->request->getData('end_time');

            //Verify there is a Service
            if($serviceId === null || $serviceId === "") {
                $this->Flash->error(__('Please select a service'));
                return $this->redirect(['action' => 'customerstylistadd', $booking_id]);
            }


            //Makes sure that the time is valid
            if ($startTime && $endTime) {
                $startTimeValue = strtotime($startTime);
                $endTimeValue = strtotime($endTime);
                if ($endTimeValue <= $startTimeValue) {
                    $this->Flash->error(__('End time must be later than start time.'));

                    return $this->redirect(['action' => 'customerstylistadd', $booking_id]);
                }
            } else {
                $this->Flash->error(__('Start or End times Cannot be blank'));
            }


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
                    'BookingsStylists.stylist_date' => $bookingDateOnly,
                    'BookingsStylists.start_time <' => $endTime,
                    'BookingsStylists.end_time >' => $startTime,
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

        $startTime = $this->request->getData('start_time');
        $endTime = $this->request->getData('end_time');
        $serviceId = $this->request->getData('service_id');
        $nameOfService = "";
        $totalServicePrice = null;

        if ($serviceId !== null && $serviceId !== "") {
            $totalServicePrice = $this->calculateServiceCost($serviceId, $startTime, $endTime);
            $nameOfService = $this->findServiceName($serviceId);
        }

        //Paginate the results if query is valid
        $filterStylists = $this->paginate($query);
        //Fetch the list of services and bookings
        $services = $this->BookingsStylists->Stylists->Services->find('list')->toArray();
        $bookings = $this->BookingsStylists->Bookings->find('list', limit: 200)->all();
        //Pass the data to the view
        $this->set(compact(
            'bookingsStylist',
            'bookings',
            'filterStylists',
            'nameOfService',
            'totalServicePrice',
            'services',
            'booking_id',
        ));
    }

    //Used to Calculate the cost of the service by the time it takes
    private function calculateServiceCost($serviceId, $startTime, $endTime)
    {
        //Obtain the cost of the service Per hour
        $servicesTable = $this->fetchTable('Services');
        $service = $servicesTable->get($serviceId);
        $serviceCost = $service->service_cost;

        //Obtain the total cost
        $startTimeValue = new DateTime($startTime);
        $endTimeValue = new DateTime($endTime);
        $difference = $endTimeValue->diff($startTimeValue);
        $hours = $difference->h;
        if ($difference->i > 0) {
            $hours ++;
        }

        return number_format($hours * $serviceCost,2);
    }

    //Used to find the service name from given id of stylist
    private function findServiceName($serviceId)
    {
        $servicesTable = $this->fetchTable('Services');
        $service = $servicesTable->get($serviceId);
        return $service->service_name;
    }

    //This is to add a stylist into the bookings Stylists Controller
    public function addStylist($bookingId)
    {
        if ($this->request->is('post')) {
            // This is to add the stylist to
            $booking = $this->BookingsStylists->Bookings->get($bookingId);
            $newStylistBooking = $this->BookingsStylists->newEmptyEntity();
            $newStylistBooking->stylist_id = $this->request->getData('stylist_id');
            $newStylistBooking->booking_id = $bookingId;
            $newStylistBooking->start_time = $this->request->getData('start_time');
            $newStylistBooking->end_time = $this->request->getData('end_time');

            $stylistId = $this->request->getData('stylist_id');
            $serviceId = $this->request->getData('service_id');
            $servicesTable = $this->fetchTable('Services');
            $service = $servicesTable->get($serviceId);
            $totalServicePrice = $this->calculateServiceCost($serviceId, $this->request->getData('start_time'), $this->request->getData('end_time'));

            //Ensure still a Float and add to entity record
            $numberServicePrice = (float)$totalServicePrice;
            if (is_numeric($numberServicePrice)) {
                $newStylistBooking->selected_cost = number_format($numberServicePrice, 2);

                $this->addPriceBooking($bookingId, $numberServicePrice);
            }
            $newStylistBooking->stylist_date = $booking->booking_date;
            $this->addServices($bookingId,$serviceId,$totalServicePrice,$stylistId);

            if ($this->BookingsStylists->save($newStylistBooking)) {
                $this->Flash->success(__('Stylist added successfully.'));
            } else {
                $this->Flash->error(__('Failed to add Stylist.'));
            }

            return $this->redirect(['action' => 'customerstylistadd', $bookingId]);
        }
    }

    //Add the Price into the booking and updates remaining cost
    private function addPriceBooking($bookingId, $serviceCost)
    {
        $bookingsTable = $this->fetchTable('Bookings');
        $booking = $bookingsTable->get($bookingId);

        $previousTotalPrice = $booking->total_cost;
        $updatedPrice = $previousTotalPrice + $serviceCost;

        $previousRemainingCost = $booking->remaining_cost;
        $updatedRemaining = $previousRemainingCost + $serviceCost;

        $booking = $bookingsTable->patchEntity($booking, [
            'total_cost' => $updatedPrice,
            'remaining_cost' => $updatedRemaining
        ]);

        if ($bookingsTable->save($booking)) {
            $this->Flash->success(__('Price Updated successfully.'));
        } else {
            $this->Flash->error(__('Failed to add price.'));
        }
    }

    //Add into Bookings_services Table
    private function addServices($bookingId,$serviceId,$serviceCost,$stylistId) {
        $bookingsServices = $this->getTableLocator()->get('BookingsServices');
        $newServiceBooking = $bookingsServices->newEmptyEntity();
        $newServiceBooking->booking_id = $bookingId;
        $newServiceBooking->stylist_id = $stylistId;
        $newServiceBooking->service_id = $serviceId;
        $newServiceBooking->service_cost = $serviceCost;


        if ($bookingsServices->save($newServiceBooking)) {
            return;
        } else {
            $this->Flash->error(__('Failed to add Stylist.'));
        }
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
