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
    }

    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        parent::beforeFilter($event);
        // Allow users to access customer-specific actions
        $this->Authentication->addUnauthenticatedActions(['customerbooking', 'customerindex', 'customerview']);
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
            ->contain(['Customers', 'Stylists', 'Services'])
            ->order(['status' => 'ASC', 'booking_date' => 'DESC']);
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
                'Services',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ]
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

        $booking = $this->Bookings->get($id, contain: ['Stylists', 'Services']);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();

            // Calculate total cost based on services
            if (!empty($data['services']['_ids'])) {
                $totalCost = 0;
                foreach ($data['services']['_ids'] as $serviceId) {
                    $service = $this->Services->get($serviceId);
                    $totalCost += $service->service_cost;
                }
                $data['total_cost'] = $totalCost;
                $data['remaining_cost'] = $totalCost;
            }

            $booking = $this->Bookings->patchEntity($booking, $data);

            if ($this->Bookings->save($booking)) {
                // If services were updated, update BookingsServices records
                if (!empty($data['services']['_ids'])) {
                    // First delete all existing records
                    $this->fetchTable('BookingsServices')->deleteAll(['booking_id' => $booking->id]);

                    // Then create new records
                    $bookingsServicesData = [];
                    foreach ($data['services']['_ids'] as $serviceId) {
                        $service = $this->Services->get($serviceId);
                        $bookingsServicesData[] = [
                            'booking_id' => $booking->id,
                            'service_id' => $serviceId,
                            'service_cost' => $service->service_cost
                        ];
                    }

                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    $bookingsServices = $bookingsServicesTable->newEntities($bookingsServicesData);
                    if (!$bookingsServicesTable->saveMany($bookingsServices)) {
                        $this->Flash->error(__('The booking was updated, but service details could not be updated.'));
                    }
                }

                // Update any existing BookingsStylists records for this booking
                $bookingStylists = $this->BookingsStylists->find()
                    ->where(['booking_id' => $booking->id])
                    ->all();

                foreach ($bookingStylists as $bookingStylist) {
                    $bookingStylist->stylist_date = $booking->booking_date;
                    $bookingStylist->start_time = $booking->start_time;
                    $bookingStylist->end_time = $booking->end_time;

                    if (!$this->BookingsStylists->save($bookingStylist)) {
                        $this->Flash->error(__('The booking was updated, but stylist details could not be updated.'));
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
        $booking = $this->Bookings->get($id);

        // Update status to cancelled instead of deleting
        $booking = $this->Bookings->patchEntity($booking, ['status' => 'cancelled']);
        if ($this->Bookings->save($booking)) {
            $this->Flash->success(__('The booking has been cancelled.'));
        } else {
            $this->Flash->error(__('The booking could not be cancelled. Please, try again.'));
        }

        return $this->redirect(['action' => 'customerindex']);
    }

    public function customerbooking() {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();
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
                            $this->Flash->error(__('The booking was saved, but some service details could not be saved.'));
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
                return $this->redirect(['action' => 'customerindex']);
            }
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find(
            'list',
            keyField: 'id',
            valueField: function ($service) {
                return $service->service_name . ' ($' . $service->service_cost . ')';
            }
        )->all();
        $this->set(compact('booking', 'stylists', 'services'));
    }


    public function adminbooking() {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            //Obtains the Customer's first and last name
            $customersTable = $this->fetchTable('Customers');
            $customer = $customersTable->get($data['customer_id'], [
                'fields' => ['id', 'first_name', 'last_name']
            ]);

            $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;

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
                            $this->Flash->error(__('The booking was saved, but some service details could not be saved.'));
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
        $customersTable = $this->fetchTable('Customers');
        $customers = $customersTable->find('list', limit: 200)->all()->toArray();
        $services = $this->fetchTable('Services')->find(
            'list',
            keyField: 'id',
            valueField: function ($service) {
                return $service->service_name . ' ($' . $service->service_cost . ')';
            }
        )->all();
        $this->set(compact('booking', 'stylists', 'services','customers'));
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
        $booking = $this->Bookings->get($id, [
            'contain' => [
                'Customers',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost']
                    ]
                ]
            ]
        ]);

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
                        // Check for overlapping time slots
                        [
                            'BookingsStylists.start_time <=' => $startTime,
                            'BookingsStylists.end_time >=' => $startTime
                        ],
                        [
                            'BookingsStylists.start_time <=' => $endTime,
                            'BookingsStylists.end_time >=' => $endTime
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
}
