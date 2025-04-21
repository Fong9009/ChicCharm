<?php
declare(strict_types=1);

namespace App\Controller;
use App\Model\Table\ServicesTable;
use App\Model\Table\StylistsTable;
use App\Model\Table\BookingsStylistsTable;

/**
 * Bookings Controller
 *
 * @property \App\Model\Table\BookingsTable $Bookings
 * @property \App\Model\Table\ServicesTable $Services
 * @property \App\Model\Table\StylistsTable $Stylists
 * @property \App\Model\Table\BookingsStylistsTable $BookingsStylists
 */
class BookingsController extends AppController
{
    protected ServicesTable $Services;
    protected StylistsTable $Stylists;
    protected BookingsStylistsTable $BookingsStylists;
    public function initialize(): void
    {
        parent::initialize();
        $this->Services = $this->getTableLocator()->get('Services');
        $this->Stylists = $this->getTableLocator()->get('Stylists');
        $this->BookingsStylists = $this->getTableLocator()->get('BookingsStylists');
        $this->loadComponent('Authentication.Authentication');
        // Allow unauthenticated access to the booking route
        $this->Authentication->addUnauthenticatedActions(['booking']);
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        
        // Get the current user
        $user = $this->Authentication->getIdentity();
        
        // Define customer-specific actions
        $customerActions = ['customerbooking', 'customerindex', 'customerview', 'dashboard'];
        
        // Define admin-specific actions
        $adminActions = ['adminbooking', 'edit', 'index', 'stylistedit', 'view'];
        
        // If the current action is a customer action
        if (in_array($this->request->getParam('action'), $customerActions)) {
            // Check if user is logged in and is a customer
            if (!$user || $user->type !== 'customer') {
                $this->Flash->error('Access denied. This area is for customers only.');
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }
        }
        
        // If the current action is an admin action
        if (in_array($this->request->getParam('action'), $adminActions)) {
            // Check if user is logged in and is an admin
            if (!$user || $user->type !== 'admin') {
                $this->Flash->error('Access denied. This area is for administrators only.');
                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
            }
        }

        // Automatically update booking statuses for past bookings
        $now = new \Cake\I18n\DateTime();
        $pastBookings = $this->Bookings->find()
            ->where([
                'status' => 'active',
                'booking_date <=' => $now->format('Y-m-d'),
                'end_time <' => $now->format('H:i:s')
            ]);

        foreach ($pastBookings as $booking) {
            $booking->status = 'finished';
            $this->Bookings->save($booking);
        }
    }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function index()
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $query = $this->Bookings->find()
            ->select([
                'Bookings.id',
                'Bookings.booking_name',
                'Bookings.booking_date',
                'Bookings.start_time',
                'Bookings.end_time',
                'Bookings.total_cost',
                'Bookings.status',
                'Bookings.notes'
            ])
            ->where(['status' => 'active'])
            ->contain([
                'Customers' => [
                    'fields' => ['id', 'first_name', 'last_name']
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost']
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ])
            ->order(['booking_date' => 'ASC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }

    public function customerindex()
    {
        $query = $this->Bookings->find()
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
            ]);
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
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $booking = $this->Bookings->get($id, contain: ['Customers', 'Stylists', 'Services']);
        $this->set(compact('booking'));
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null|void Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            $booking = $this->Bookings->patchEntity($booking, $data);

            if ($this->Bookings->save($booking)) {
                // Get the stylist_id if it's set in the form
                if (!empty($data['stylist_id'])) {
                    // Create a BookingsStylists record with the booking times
                    $bookingStylist = $this->BookingsStylists->newEmptyEntity();
                    $bookingStylistData = [
                        'booking_id' => $booking->id,
                        'stylist_id' => $data['stylist_id'],
                        'stylist_date' => $booking->booking_date,
                        'start_time' => $booking->start_time,
                        'end_time' => $booking->end_time,
                        'selected_cost' => $booking->total_cost
                    ];

                    $bookingStylist = $this->BookingsStylists->patchEntity($bookingStylist, $bookingStylistData);
                    if (!$this->BookingsStylists->save($bookingStylist)) {
                        $this->Flash->error(__('The booking was saved, but stylist details could not be saved.'));
                    }
                }

                $this->Flash->success(__('The booking has been saved.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find(
            'list',
            keyField: 'id',
            valueField: function ($service) {
                return $service->service_name . ' ($' . $service->service_cost . ')';
            }
        )->all();
        $this->set(compact('booking', 'customers', 'stylists', 'services'));
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
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $booking = $this->Bookings->get($id, contain: [
            'Customers',
            'BookingsStylists' => ['Stylists'],
            'BookingsServices' => ['Services', 'Stylists']
        ]);

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Check if end time exceeds 5 PM
            if (isset($data['end_time'])) {
                $endTime = new \DateTime($data['end_time']);
                $closingTime = new \DateTime('17:00');

                if ($endTime > $closingTime) {
                    $this->Flash->error(__('Booking cannot extend past 5 PM as the shop will be closed.'));
                    return $this->redirect(['action' => 'edit', $id]);
                }
            }

            // Get customer details and set booking name
            if (isset($data['customer_id'])) {
                $customer = $this->Bookings->Customers->get($data['customer_id']);
                $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;
            }

            // Calculate total cost from all selected services
            $totalCost = 0;
            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    $totalCost += floatval($serviceData['service_cost']);
                }
            }

            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? null;

            // Delete existing BookingsServices and BookingsStylists
            $bookingsServicesTable = $this->fetchTable('BookingsServices');
            $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
            
            $bookingsServicesTable->deleteAll(['booking_id' => $booking->id]);
            $bookingsStylistsTable->deleteAll(['booking_id' => $booking->id]);

            $booking = $this->Bookings->patchEntity($booking, $data);
            if ($this->Bookings->save($booking)) {
                // Save BookingsServices records with stylist assignments
                if (!empty($data['bookings_services'])) {
                    foreach ($data['bookings_services'] as $serviceData) {
                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $booking->id,
                            'service_id' => $serviceData['service_id'],
                            'stylist_id' => $serviceData['stylist_id'],
                            'service_cost' => $serviceData['service_cost']
                        ]);

                        if (!$bookingsServicesTable->save($bookingService)) {
                            \Cake\Log\Log::error('Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                            $this->Flash->error(__('The booking was updated, but some service details could not be saved.'));
                        }
                    }
                }

                // Create BookingsStylists records for each selected stylist
                if (!empty($data['bookings_services'])) {
                    $processedStylists = [];

                    foreach ($data['bookings_services'] as $serviceData) {
                        $stylistId = $serviceData['stylist_id'];

                        // Only create one BookingsStylists record per stylist
                        if (!in_array($stylistId, $processedStylists)) {
                            $bookingStylist = $bookingsStylistsTable->newEntity([
                                'booking_id' => $booking->id,
                                'stylist_id' => $stylistId,
                                'stylist_date' => $booking->booking_date,
                                'start_time' => $booking->start_time,
                                'end_time' => $booking->end_time,
                                'selected_cost' => $booking->total_cost
                            ]);

                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                $this->Flash->error(__('The booking was updated, but some stylist details could not be saved.'));
                            }

                            $processedStylists[] = $stylistId;
                        }
                    }
                }

                $this->Flash->success(__('The booking has been updated successfully.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The booking could not be updated. Please, try again.'));
        }

        // No need to format the date here as CakePHP will handle it through the form helper
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'stylists', 'services', 'customers'));
    }

    //Remove Stylists from a booking
    public function removeStylist($stylistId, $bookingId) {
        $bookServicesTable = $this->fetchTable('BookingsServices');
        $bookStylistTable = $this->fetchTable('BookingsStylists');

        //Obtains the booking from the booking table given booking id
        $bookingsTable = $this->fetchTable('Bookings');
        $booking = $bookingsTable->find()->where(['id' => $bookingId])->first();

        //Obtains the booking service from the booking service table
        $bookingService = $bookServicesTable
            ->find()
            ->where([
                'stylist_id' => $stylistId,
                'booking_id' => $bookingId
            ])->first();

        //Obtains the booking Stylist from the booking Stylist table
        $bookingStylist = $bookStylistTable
            ->find()
            ->where([
                'stylist_id' => $stylistId,
                'booking_id' => $bookingId
            ])->first();

        if ($bookingService && $bookingStylist && $booking) {
            //Remove the service cost from the booking
            $serviceCost = $bookingService->service_cost;
            $booking->total_cost -= $serviceCost;
            if ($booking->total_cost < 0) {
                $booking->total_cost = 0;
            }

            $booking->remaining_cost -= $serviceCost;
            if ($booking->remaining_cost < 0) {
                $booking->remaining_cost = 0;
            }

            //Save the new updated booking cost
            if ($this->Bookings->save($booking)) {
                //Delete the service From Booking service
                if ($bookServicesTable->delete($bookingService) &&  $bookStylistTable->delete($bookingStylist)) {
                    $this->Flash->success('Stylist/Service removed and booking updated.');
                } else {
                    $this->Flash->error('Stylist/Service could not be deleted.');
                }
            } else {
                $this->Flash->error('Booking could not be updated.');
            }
        } else {
            $this->Flash->error('Booking could not be updated.');
        }
        return $this->redirect(['action' => 'edit', $bookingId]);
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
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $this->request->allowMethod(['post', 'delete']);
        $booking = $this->Bookings->get($id);

        // Allow deletion of both cancelled and finished bookings
        if ($booking->status !== 'cancelled' && $booking->status !== 'finished') {
            $this->Flash->error(__('Only cancelled or finished bookings can be deleted.'));
            return $this->redirect(['action' => 'index']);
        }

        if ($this->Bookings->delete($booking)) {
            $this->Flash->success(__('The booking has been deleted.'));
        } else {
            $this->Flash->error(__('The booking could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'adminPastBookings']);
    }

    public function customerdelete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $booking = $this->Bookings->get($id, [
            'contain' => ['BookingsStylists']
        ]);

        // Delete the associated BookingsStylists records
        if (!empty($booking->bookings_stylists)) {
            $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
            foreach ($booking->bookings_stylists as $bookingStylist) {
                $bookingsStylistsTable->delete($bookingStylist);
            }
        }

        // Update status to cancelled
        $booking = $this->Bookings->patchEntity($booking, ['status' => 'cancelled']);
        if ($this->Bookings->save($booking)) {
            $this->Flash->success(__('The booking has been cancelled.'));
        } else {
            $this->Flash->error(__('The booking could not be cancelled. Please, try again.'));
        }

        // Check where the request came from
        $referer = $this->request->getHeader('Referer');
        if (strpos($referer[0], 'dashboard') !== false) {
            return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
        } else {
            return $this->redirect(['action' => 'customerindex']);
        }
    }

    public function customerbooking() {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Check if end time exceeds 5 PM
            if (isset($data['end_time'])) {
                $endTime = new \DateTime($data['end_time']);
                $closingTime = new \DateTime('17:00');

                if ($endTime > $closingTime) {
                    $this->Flash->error(__('Booking cannot extend past 5 PM as the shop will be closed.'));
                    return $this->redirect(['action' => 'customerbooking']);
                }
            }

            // Automatically set customer details
            $user = $this->Authentication->getIdentity();
            $data['customer_id'] = $user->id;
            $data['booking_name'] = 'Booking for ' . $user->first_name . ' ' . $user->last_name;

            // Format the date to Y-m-d format
            if (isset($data['booking_date'])) {
                $date = new \DateTime($data['booking_date']);
                $data['booking_date'] = $date->format('Y-m-d');
            }

            // Calculate total cost from all selected services
            $totalCost = 0;

            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    $totalCost += floatval($serviceData['service_cost']);
                }
            }

            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? null;

            $booking = $this->Bookings->patchEntity($booking, $data);
            if ($this->Bookings->save($booking)) {
                // Save BookingsServices records with stylist assignments
                if (!empty($data['bookings_services'])) {
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceData) {
                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $booking->id,
                            'service_id' => $serviceData['service_id'],
                            'stylist_id' => $serviceData['stylist_id'],
                            'service_cost' => $serviceData['service_cost']
                        ]);

                        if (!$bookingsServicesTable->save($bookingService)) {
                            \Cake\Log\Log::error('Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                            $this->Flash->error(__('The booking was saved, but some service details could not be saved.'));
                        } else {
                            \Cake\Log\Log::debug('Successfully saved booking service');
                        }
                    }
                }

                // Create BookingsStylists records for each selected stylist
                if (!empty($data['bookings_services'])) {
                    $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                    $processedStylists = [];

                    foreach ($data['bookings_services'] as $serviceData) {
                        $stylistId = $serviceData['stylist_id'];

                        // Only create one BookingsStylists record per stylist
                        if (!in_array($stylistId, $processedStylists)) {
                            $bookingStylist = $bookingsStylistsTable->newEntity([
                                'booking_id' => $booking->id,
                                'stylist_id' => $stylistId,
                                'stylist_date' => $booking->booking_date,
                                'start_time' => $booking->start_time,
                                'end_time' => $booking->end_time,
                                'selected_cost' => $booking->total_cost
                            ]);

                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                $this->Flash->error(__('Your booking was saved, but some stylist details could not be saved.'));
                            }

                            $processedStylists[] = $stylistId;
                        }
                    }
                }

                $this->Flash->success(__('Your booking has been saved successfully.'));
                return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'stylists', 'services'));
    }


    public function adminbooking() {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            // Check if end time exceeds 5 PM
            if (isset($data['end_time'])) {
                $endTime = new \DateTime($data['end_time']);
                $closingTime = new \DateTime('17:00');

                if ($endTime > $closingTime) {
                    $this->Flash->error(__('Booking cannot extend past 5 PM as the shop will be closed.'));
                    return $this->redirect(['action' => 'adminbooking']);
                }
            }

            // Format the date to Y-m-d format
            if (isset($data['booking_date'])) {
                $date = new \DateTime($data['booking_date']);
                $data['booking_date'] = $date->format('Y-m-d');
            }

            // Get customer details and set booking name
            if (isset($data['customer_id'])) {
                $customer = $this->Bookings->Customers->get($data['customer_id']);
                $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;
            }

            // Calculate total cost from all selected services
            $totalCost = 0;

            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    $totalCost += floatval($serviceData['service_cost']);
                }
            }

            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? null;

            $booking = $this->Bookings->patchEntity($booking, $data);
            if ($this->Bookings->save($booking)) {
                // Save BookingsServices records with stylist assignments
                if (!empty($data['bookings_services'])) {
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceData) {
                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $booking->id,
                            'service_id' => $serviceData['service_id'],
                            'stylist_id' => $serviceData['stylist_id'],
                            'service_cost' => $serviceData['service_cost']
                        ]);

                        if (!$bookingsServicesTable->save($bookingService)) {
                            \Cake\Log\Log::error('Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                            $this->Flash->error(__('The booking was saved, but some service details could not be saved.'));
                        } else {
                            \Cake\Log\Log::debug('Successfully saved booking service');
                        }
                    }
                }

                // Create BookingsStylists records for each selected stylist
                if (!empty($data['bookings_services'])) {
                    $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                    $processedStylists = [];

                    foreach ($data['bookings_services'] as $serviceData) {
                        $stylistId = $serviceData['stylist_id'];

                        // Only create one BookingsStylists record per stylist
                        if (!in_array($stylistId, $processedStylists)) {
                            $bookingStylist = $bookingsStylistsTable->newEntity([
                                'booking_id' => $booking->id,
                                'stylist_id' => $stylistId,
                                'stylist_date' => $booking->booking_date,
                                'start_time' => $booking->start_time,
                                'end_time' => $booking->end_time,
                                'selected_cost' => $booking->total_cost
                            ]);

                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                $this->Flash->error(__('Your booking was saved, but some stylist details could not be saved.'));
                            }

                            $processedStylists[] = $stylistId;
                        }
                    }
                }

                $this->Flash->success(__('Your booking for the customer has been saved successfully.'));
                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }

        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'stylists', 'services', 'customers'));
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
        $booking = $this->Bookings->get(
            $id,
            contain: [
                'Customers',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost']
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ]
        );

        $this->set(compact('booking'));
    }

    /**
     * Select Service method
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function selectService()
    {
        $services = $this->Services->find('all');
        $this->set(compact('services'));
    }

    /**
     * Select Contractor method
     *
     * @param int $serviceId Service ID
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function selectContractor($serviceId)
    {
        $service = $this->Services->get($serviceId);
        $stylists = $this->Stylists->find()
            ->matching('Services', function ($q) use ($serviceId) {
                return $q->where(['Services.id' => $serviceId]);
            });

        $this->set(compact('service', 'stylists'));
    }

    /**
     * Select Date method
     *
     * @param int $serviceId Service ID
     * @param int $stylistId Stylist ID
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function selectDate($serviceId, $stylistId)
    {
        $service = $this->Services->get($serviceId);
        $stylist = $this->Stylists->get($stylistId);

        // Get existing bookings for the stylist to show unavailable dates
        $existingBookings = $this->BookingsStylists->find()
            ->where(['stylist_id' => $stylistId])
            ->select(['stylist_date'])
            ->distinct()
            ->toArray();

        $this->set(compact('service', 'stylist', 'existingBookings'));
    }

    /**
     * Get Stylists method
     *
     * @param int|null $serviceId Service ID
     * @return \Cake\Http\Response|null|void Returns JSON response with stylists
     */
    public function getStylists()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $data = $this->request->getData();
        $serviceIds = $data['service_ids'] ?? [];
        $bookingDate = $data['booking_date'] ?? null;
        $startTime = $data['start_time'] ?? null;
        $endTime = $data['end_time'] ?? null;

        if (empty($serviceIds) || !$bookingDate || !$startTime || !$endTime) {
            return $this->response->withStringBody(json_encode([]));
        }

        // Check if end time exceeds 5 PM
        $endTimeObj = new \DateTime($endTime);
        $closingTime = new \DateTime('17:00'); // 5 PM

        if ($endTimeObj > $closingTime) {
            return $this->response->withStringBody(json_encode([
                'message' => 'Note: The shop closes at 5 PM. Please select an earlier time slot.',
                'stylists' => []
            ]));
        }

        try {
            // First check if the services exist
            $invalidServiceIds = [];
            $validServices = [];

            foreach ($serviceIds as $serviceId) {
                try {
                    $service = $this->Services->get($serviceId);
                    $validServices[] = $service;
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    $invalidServiceIds[] = $serviceId;
                    $this->log('Service not found: ' . $serviceId . ' - ' . $e->getMessage());
                }
            }

            if (!empty($invalidServiceIds)) {
                return $this->response->withStringBody(json_encode([
                    'error' => 'One or more services not found: ' . implode(', ', $invalidServiceIds)
                ]));
            }

            // Get all stylists first to check if there are any
            $allStylists = $this->Stylists->find()->count();
            $this->log('Total stylists in database: ' . $allStylists);

            // Start with all stylists
            $stylistsQuery = $this->Stylists->find()
                ->select(['Stylists.id', 'Stylists.first_name', 'Stylists.last_name'])
                ->distinct(['Stylists.id']);

            // Filter for stylists who can provide all the selected services
            foreach ($serviceIds as $serviceId) {
                $stylistsQuery->innerJoinWith('Services', function ($q) use ($serviceId) {
                    return $q->where(['Services.id' => $serviceId]);
                });
            }

            // Filter out stylists who have overlapping bookings
            $stylistsQuery->notMatching('BookingsStylists', function ($q) use ($bookingDate, $startTime, $endTime) {
                return $q->where([
                    'BookingsStylists.stylist_date' => $bookingDate,
                    'OR' => [
                        // Check for overlapping time slots, allowing exact end time matches
                        [
                            'BookingsStylists.start_time <' => $startTime,
                            'BookingsStylists.end_time >' => $startTime
                        ],
                        [
                            'BookingsStylists.start_time <' => $endTime,
                            'BookingsStylists.end_time >' => $endTime
                        ],
                        [
                            'BookingsStylists.start_time >=' => $startTime,
                            'BookingsStylists.end_time <=' => $endTime
                        ]
                    ],
                    'BookingsStylists.start_time IS NOT NULL',
                    'BookingsStylists.end_time IS NOT NULL'
                ]);
            });

            $stylists = $stylistsQuery->toArray();

            $this->log('Available stylists for services ' . implode(',', $serviceIds) . ': ' . count($stylists));

            // Format the results
            $formattedStylists = array_map(function ($stylist) {
                return [
                    'id' => $stylist->id,
                    'name' => $stylist->first_name . ' ' . $stylist->last_name
                ];
            }, $stylists);

            return $this->response->withStringBody(json_encode($formattedStylists));
        } catch (\Exception $e) {
            $this->log('Error in getStylists: ' . $e->getMessage());
            $this->log('Stack trace: ' . $e->getTraceAsString());
            return $this->response->withStatus(500)
                ->withStringBody(json_encode(['error' => 'An error occurred while fetching stylists: ' . $e->getMessage()]));
        }
    }

    public function dashboard()
    {
        $query = $this->Bookings->find()
            ->where([
                'customer_id' => $this->Authentication->getIdentity()->id,
                'status' => 'active'
            ])
            ->contain([
                'Customers',
                'Services',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ])
            ->order(['booking_date' => 'ASC'])
            ->limit(5);  // Only show the next 5 upcoming bookings

        $bookings = $query->all();
        $this->set(compact('bookings'));
    }

    /**
     * Get Available Time Slots method
     *
     * @return \Cake\Http\Response|null|void Returns JSON response with available time slots
     */
    public function getAvailableTimeSlots()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $data = $this->request->getData();
        $date = $data['date'] ?? null;
        $serviceIds = $data['service_ids'] ?? [];

        if (!$date || empty($serviceIds)) {
            return $this->response->withStringBody(json_encode([]));
        }

        try {
            // Get all services to calculate total duration
            $totalDuration = 0;
            foreach ($serviceIds as $serviceId) {
                $service = $this->Services->get($serviceId);
                $totalDuration += $service->duration_minutes;
            }

            // Get all bookings for the selected date
            $existingBookings = $this->BookingsStylists->find()
                ->where([
                    'stylist_date' => $date,
                    'BookingsStylists.start_time IS NOT NULL',
                    'BookingsStylists.end_time IS NOT NULL'
                ])
                ->select(['start_time', 'end_time'])
                ->toArray();

            // Generate all possible time slots from 9 AM to 5 PM in 15-minute intervals
            $availableSlots = [];
            $startHour = 9;
            $endHour = 17;
            $interval = 15;

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                for ($minute = 0; $minute < 60; $minute += $interval) {
                    $slotStart = sprintf('%02d:%02d', $hour, $minute);

                    // Calculate slot end time
                    $endTime = strtotime("+{$totalDuration} minutes", strtotime($slotStart));
                    $slotEnd = date('H:i', $endTime);

                    // Allow bookings that end at exactly 5 PM (17:00)
                    if (strtotime($slotEnd) > strtotime('17:00')) {
                        continue;
                    }

                    // Check if slot overlaps with any existing booking
                    $isAvailable = true;
                    foreach ($existingBookings as $booking) {
                        $bookingStart = $booking->start_time->format('H:i');
                        $bookingEnd = $booking->end_time->format('H:i');

                        // Convert times to timestamps for comparison
                        $slotStartTime = strtotime($slotStart);
                        $slotEndTime = strtotime($slotEnd);
                        $bookingStartTime = strtotime($bookingStart);
                        $bookingEndTime = strtotime($bookingEnd);

                        // Check for overlap, allowing exact end time matches
                        if (
                            ($slotStartTime >= $bookingStartTime && $slotStartTime < $bookingEndTime) ||
                            ($slotEndTime > $bookingStartTime && $slotEndTime <= $bookingEndTime) ||
                            ($slotStartTime <= $bookingStartTime && $slotEndTime >= $bookingEndTime)
                        ) {
                            $isAvailable = false;
                            break;
                        }
                    }

                    if ($isAvailable) {
                        $availableSlots[] = [
                            'value' => $slotStart,
                            'display' => date('g:i A', strtotime($slotStart))
                        ];
                    }
                }
            }

            return $this->response->withStringBody(json_encode($availableSlots));

        } catch (\Exception $e) {
            $this->log('Error in getAvailableTimeSlots: ' . $e->getMessage());
            return $this->response->withStatus(500)
                ->withStringBody(json_encode(['error' => 'An error occurred while fetching available time slots']));
        }
    }

    /**
     * Update booking statuses to finished for past bookings
     *
     * @return void
     */
    public function updateBookingStatuses()
    {
        $now = new \Cake\I18n\DateTime();
        
        // Find all active bookings that have ended
        $pastBookings = $this->Bookings->find()
            ->where([
                'status' => 'active',
                'booking_date <=' => $now->format('Y-m-d'),
                'end_time <' => $now->format('H:i:s')
            ]);

        foreach ($pastBookings as $booking) {
            $booking->status = 'finished';
            $this->Bookings->save($booking);
        }
    }

    /**
     * View past bookings (Admin only)
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function adminPastBookings()
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $query = $this->Bookings->find()
            ->select([
                'Bookings.id',
                'Bookings.booking_name',
                'Bookings.booking_date',
                'Bookings.start_time',
                'Bookings.end_time',
                'Bookings.total_cost',
                'Bookings.status',
                'Bookings.notes'
            ])
            ->where(['status IN' => ['finished', 'cancelled']])
            ->contain([
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost']
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ])
            ->order(['booking_date' => 'DESC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }

    /**
     * View customer's past bookings
     *
     * @return \Cake\Http\Response|null|void Renders view
     */
    public function customerPastBookings()
    {
        // Get the current logged-in customer
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'customer') {
            $this->Flash->error('Access denied. Please log in as a customer.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'home']);
        }

        $query = $this->Bookings->find()
            ->where([
                'customer_id' => $user->id,
                'status IN' => ['finished', 'cancelled']
            ])
            ->contain([
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
            ])
            ->order(['booking_date' => 'DESC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }

    public function booking()
    {
        $user = $this->Authentication->getIdentity();
        if (!$user) {
            $this->Flash->error('Please login to make a booking.');
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }

        if ($user->type === 'customer') {
            return $this->redirect(['action' => 'customerbooking']);
        } elseif ($user->type === 'admin') {
            return $this->redirect(['action' => 'adminbooking']);
        } else {
            $this->Flash->error('Invalid user type for booking.');
            return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'landing']);
        }
    }
}
