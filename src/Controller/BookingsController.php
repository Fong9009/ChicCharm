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
        $dateString = $now->format('Y-m-d');
        $timeString = $now->format('H:i:s');

        // Find active bookings where the date is today or earlier
        $potentiallyFinishedBookings = $this->Bookings->find()
            ->where([
                'Bookings.status' => 'active',
                'Bookings.booking_date <=' => $dateString,
            ])
            ->contain(['BookingsServices'])
            ->all();

        foreach ($potentiallyFinishedBookings as $booking) {
            if ($booking->bookings_services->isEmpty()) {
                // If an active booking somehow has no services, mark as finished if date is past
                if ($booking->booking_date->format('Y-m-d') < $dateString) {
                    $booking->status = 'finished';
                     $this->Bookings->save($booking);
                }
                continue; // Skip bookings with no services for time checks
            }

            $latestEndTime = null;
            foreach ($booking->bookings_services as $bs) {
                if ($bs->end_time) {
                    $currentServiceEndTime = \Cake\I18n\FrozenTime::parse($bs->end_time->format('H:i:s')); // Ensure it's Time object
                    if ($latestEndTime === null || $currentServiceEndTime->gt($latestEndTime)) {
                        $latestEndTime = $currentServiceEndTime;
                    }
                }
            }

            // Check if the booking date is past OR if it's today and the latest end time is past
            if (
                $booking->booking_date->format('Y-m-d') < $dateString ||
                ($booking->booking_date->format('Y-m-d') == $dateString && $latestEndTime !== null && $latestEndTime->format('H:i:s') < $timeString)
            ) {
                $booking->status = 'finished';
                $this->Bookings->save($booking);
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
                'booking_date' => 'ASC'
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

        $booking = $this->Bookings->get($id, contain: ['Customers', 'BookingsServices', 'BookingsStylists']);
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
            $bookingId = $booking->id;

            // Format the date to Y-m-d format
            if (isset($data['booking_date'])) {
                $bookingDate = new \DateTime($data['booking_date']);
                $data['booking_date_formatted'] = $bookingDate->format('Y-m-d');
            } else {
                $this->Flash->error(__('Booking date is missing.'));
                return $this->redirect(['action' => 'edit', $bookingId]);
            }

            // Get customer details and set booking name
            if (isset($data['customer_id'])) {
                try {
                    $customer = $this->Bookings->Customers->get($data['customer_id']);
                    $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    $this->Flash->error(__('Selected customer not found.'));
                    return $this->redirect(['action' => 'edit', $bookingId]);
                }
            } else {
                // Use existing customer if not provided in form (should usually be there)
                 if ($booking->customer_id) {
                    $data['customer_id'] = $booking->customer_id;
                    $data['booking_name'] = $booking->booking_name; // Keep existing name
                 } else {
                    $this->Flash->error(__('Customer ID is missing.'));
                    return $this->redirect(['action' => 'edit', $bookingId]);
                 }
            }

            // Calculate total cost from all selected services
            $totalCost = 0;
            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    $totalCost += floatval($serviceData['service_cost'] ?? 0);
                }
            }
            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost; // Recalculate remaining cost based on new total
            $data['notes'] = $data['notes'] ?? null;

            // Check if any service data was submitted (might be empty if all services unchecked)
            $hasServiceData = !empty($data['bookings_services']);

            // --- Start Transaction (Recommended for multi-table operations) ---
            $connection = $this->Bookings->getConnection();
            try {
                $connection->begin();

                // Delete existing BookingsServices and BookingsStylists
                $bookingsServicesTable = $this->fetchTable('BookingsServices');
                $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                $bookingsServicesTable->deleteAll(['booking_id' => $bookingId]);
                $bookingsStylistsTable->deleteAll(['booking_id' => $bookingId]);

                // Patch main booking entity with non-time fields first
                 $booking = $this->Bookings->patchEntity($booking, [
                    'customer_id' => $data['customer_id'],
                    'booking_name' => $data['booking_name'],
                    'booking_date' => $data['booking_date_formatted'],
                    'total_cost' => $data['total_cost'],
                    'remaining_cost' => $data['remaining_cost'],
                    'notes' => $data['notes']
                ], ['associated' => []]);

                // Save main booking changes (without times yet)
                if (!$this->Bookings->save($booking)) {
                    throw new \Exception('Failed to save main booking updates.');
                }

                $allServicesTimes = []; // To store start/end times
                // Fetch service durations if we have services
                $servicesDetails = [];
                if ($hasServiceData) {
                    $serviceIds = array_column($data['bookings_services'], 'service_id');
                    if (!empty($serviceIds)) {
                        $servicesDetails = $this->Services->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'duration_minutes'
                        ])->where(['id IN' => $serviceIds])->toArray();
                    }

                    // Re-Save BookingsServices records with individual start/end times
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            \Cake\Log\Log::warning('[Edit] Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }
                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;
                        if ($duration <= 0) {
                            \Cake\Log\Log::warning("[Edit] Skipping service ID {$serviceId} with zero duration.");
                            continue;
                        }
                        $startTime = new \DateTime($data['booking_date_formatted'] . ' ' . $startTimeString);
                        $endTime = clone $startTime;
                        $endTime->modify("+{$duration} minutes");

                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $bookingId,
                            'service_id' => $serviceId,
                            'stylist_id' => (int)$serviceData['stylist_id'],
                            'start_time' => $startTime->format('H:i:s'),
                            'end_time' => $endTime->format('H:i:s'),
                            'service_cost' => $serviceData['service_cost']
                        ]);
                        if (!$bookingsServicesTable->save($bookingService)) {
                            // Rollback on failure
                            throw new \Exception('Failed to save updated booking service details.');
                        }
                         $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                    } // end foreach bookings_services
                } // end if hasServiceData

                // Calculate and update overall start/end time
                $overallStartTime = null;
                $overallEndTime = null;
                if (!empty($allServicesTimes)) {
                    $overallStartTime = min(array_column($allServicesTimes, 'start'));
                    $overallEndTime = max(array_column($allServicesTimes, 'end'));
                }
                 // Update booking with potentially null times if no services selected
                 $booking->start_time = $overallStartTime ? $overallStartTime->format('H:i:s') : null;
                 $booking->end_time = $overallEndTime ? $overallEndTime->format('H:i:s') : null;
                 if (!$this->Bookings->save($booking)) {
                     throw new \Exception('Failed to save overall times on booking update.');
                 }

                // Re-Create BookingsStylists records
                if ($hasServiceData) {
                     $processedStylists = [];
                     foreach ($data['bookings_services'] as $serviceData) {
                         if (!isset($serviceData['stylist_id'])) continue;
                         $stylistId = (int)$serviceData['stylist_id'];
                         if (!in_array($stylistId, $processedStylists)) {
                             $bookingStylist = $bookingsStylistsTable->newEntity([
                                 'booking_id' => $bookingId,
                                 'stylist_id' => $stylistId,
                                 'stylist_date' => $booking->booking_date->format('Y-m-d'),
                                 'selected_cost' => $booking->total_cost
                             ]);
                             if (!$bookingsStylistsTable->save($bookingStylist)) {
                                 throw new \Exception('Failed to save updated booking stylist details.');
                             }
                             $processedStylists[] = $stylistId;
                         }
                     }
                 }

                // Commit transaction
                $connection->commit();
                $this->Flash->success(__('The booking has been updated successfully.'));
                return $this->redirect(['action' => 'index']);

            } catch (\Exception $e) {
                // Rollback transaction on any error
                $connection->rollback();
                \Cake\Log\Log::error('[Edit] Booking update failed: ' . $e->getMessage() . ' Booking ID: ' . $bookingId . ' Data: ' . json_encode($data));
                $this->Flash->error(__('The booking could not be updated. Please, try again. Error: {0}', $e->getMessage()));
                 // Repopulate form data for rendering
                 $booking->setErrors(json_decode($e->getMessage(), true) ?: []);
            }
            // --- End Transaction ---

        } // end if request is post/put/patch

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

            // Create a temporary booking entity without associations first
            $booking = $this->Bookings->newEntity([
                'customer_id' => $data['customer_id'],
                'booking_name' => $data['booking_name'],
                'booking_date' => $data['booking_date'],
                'total_cost' => $data['total_cost'],
                'remaining_cost' => $data['remaining_cost'],
                'notes' => $data['notes'] ?? null,
                'status' => 'active' 
            ]);

            // Try saving the main booking record
            if ($this->Bookings->save($booking)) {
                $bookingId = $booking->id;
                $allServicesTimes = [];

                // Fetch service durations
                $serviceIds = array_column($data['bookings_services'] ?? [], 'service_id');
                $servicesDetails = [];
                if (!empty($serviceIds)) {
                    $servicesDetails = $this->Services->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes'
                    ])->where(['id IN' => $serviceIds])->toArray();
                }

                // Save BookingsServices records with individual start/end times
                if (!empty($data['bookings_services'])) {
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                        // Ensure all needed keys exist
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            \Cake\Log\Log::warning('Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }

                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time']; 
                        $duration = $servicesDetails[$serviceId] ?? 0;

                        if ($duration <= 0) {
                            \Cake\Log\Log::warning("Skipping service ID {$serviceId} with zero or invalid duration.");
                            continue;
                        }

                        try {
                            $startTime = new \DateTime($data['booking_date'] . ' ' . $startTimeString);
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");

                            $bookingService = $bookingsServicesTable->newEntity([
                                'booking_id' => $bookingId,
                                'service_id' => $serviceId,
                                'stylist_id' => (int)$serviceData['stylist_id'],
                                'start_time' => $startTime->format('H:i:s'),
                                'end_time' => $endTime->format('H:i:s'),
                                'service_cost' => $serviceData['service_cost']
                            ]);

                            if ($bookingsServicesTable->save($bookingService)) {
                                $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                                \Cake\Log\Log::debug('Successfully saved booking service with times');
                            } else {
                                \Cake\Log\Log::error('Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                                // Decide if we should rollback or just flag the error
                            }
                        } catch (\Exception $e) {
                            \Cake\Log\Log::error("Error processing time for service {$serviceId}: " . $e->getMessage());
                        }
                    }
                }

                // Create BookingsStylists records (without overall times)
                if (!empty($data['bookings_services'])) {
                     $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                     $processedStylists = [];
                     foreach ($data['bookings_services'] as $serviceData) {
                         if (!isset($serviceData['stylist_id'])) continue;
                         $stylistId = (int)$serviceData['stylist_id'];

                         if (!in_array($stylistId, $processedStylists)) {
                             $bookingStylist = $bookingsStylistsTable->newEntity([
                                 'booking_id' => $bookingId,
                                 'stylist_id' => $stylistId,
                                 'stylist_date' => $booking->booking_date->format('Y-m-d'),
                                 'selected_cost' => $booking->total_cost
                             ]);
                             if (!$bookingsStylistsTable->save($bookingStylist)) {
                                  \Cake\Log\Log::error('Failed to save booking stylist record for stylist ' . $stylistId . ' Errors: ' . json_encode($bookingStylist->getErrors()));
                                 $this->Flash->error(__('Your booking was saved, but some stylist details could not be saved.'));
                             }
                             $processedStylists[] = $stylistId;
                         }
                     }
                 }

                $this->Flash->success(__('Your booking has been saved successfully.'));
                return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
            }

            \Cake\Log\Log::error('Failed to save initial booking. Errors: ' . json_encode($booking->getErrors()));
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

            // Format the date to Y-m-d format
            if (isset($data['booking_date'])) {
                $bookingDate = new \DateTime($data['booking_date']);
                $data['booking_date_formatted'] = $bookingDate->format('Y-m-d');
            } else {
                $this->Flash->error(__('Booking date is missing.'));
                return $this->redirect(['action' => 'adminbooking']);
            }

            // Get customer details and set booking name
            if (isset($data['customer_id'])) {
                try {
                    $customer = $this->Bookings->Customers->get($data['customer_id']);
                    $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;
                } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
                    $this->Flash->error(__('Selected customer not found.'));
                    return $this->redirect(['action' => 'adminbooking']);
                }
            } else {
                 $this->Flash->error(__('Customer ID is missing.'));
                 return $this->redirect(['action' => 'adminbooking']);
            }

            // Calculate total cost from all selected services
            $totalCost = 0;
            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    // Ensure service_cost exists before adding
                    $totalCost += floatval($serviceData['service_cost'] ?? 0);
                }
            }
            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost; // Assuming full cost is remaining initially
            $data['notes'] = $data['notes'] ?? null;

            // Create a temporary booking entity without associations first
            $booking = $this->Bookings->newEntity([
                'customer_id' => $data['customer_id'],
                'booking_name' => $data['booking_name'],
                'booking_date' => $data['booking_date_formatted'],
                'total_cost' => $data['total_cost'],
                'remaining_cost' => $data['remaining_cost'],
                'notes' => $data['notes'] ?? null,
                'status' => 'active'
            ]);

            // Try saving the main booking record
            if ($this->Bookings->save($booking)) {
                $bookingId = $booking->id;
                $allServicesTimes = [];

                // Fetch service durations
                $serviceIds = array_column($data['bookings_services'] ?? [], 'service_id');
                $servicesDetails = [];
                if (!empty($serviceIds)) {
                    $servicesDetails = $this->Services->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes'
                    ])->where(['id IN' => $serviceIds])->toArray();
                }

                // Save BookingsServices records with individual start/end times
                if (!empty($data['bookings_services'])) {
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                        // Ensure all needed keys exist
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            \Cake\Log\Log::warning('[Admin] Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }

                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;

                        if ($duration <= 0) {
                            \Cake\Log\Log::warning("[Admin] Skipping service ID {$serviceId} with zero or invalid duration.");
                            continue;
                        }

                        try {
                            // Use the already formatted date
                            $startTime = new \DateTime($data['booking_date_formatted'] . ' ' . $startTimeString);
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");

                            $bookingService = $bookingsServicesTable->newEntity([
                                'booking_id' => $bookingId,
                                'service_id' => $serviceId,
                                'stylist_id' => (int)$serviceData['stylist_id'],
                                'start_time' => $startTime->format('H:i:s'),
                                'end_time' => $endTime->format('H:i:s'),
                                'service_cost' => $serviceData['service_cost']
                            ]);

                            if ($bookingsServicesTable->save($bookingService)) {
                                $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                                \Cake\Log\Log::debug('[Admin] Successfully saved booking service with times');
                            } else {
                                \Cake\Log\Log::error('[Admin] Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                                // Consider adding a flash message here too
                            }
                        } catch (\Exception $e) {
                            \Cake\Log\Log::error("[Admin] Error processing time for service {$serviceId}: " . $e->getMessage());
                        }
                    }
                }

                // Create BookingsStylists records (without overall times)
                if (!empty($data['bookings_services'])) {
                     $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                     $processedStylists = [];
                     foreach ($data['bookings_services'] as $serviceData) {
                         if (!isset($serviceData['stylist_id'])) continue;
                         $stylistId = (int)$serviceData['stylist_id'];

                         if (!in_array($stylistId, $processedStylists)) {
                             $bookingStylist = $bookingsStylistsTable->newEntity([
                                 'booking_id' => $bookingId,
                                 'stylist_id' => $stylistId,
                                 'stylist_date' => $booking->booking_date->format('Y-m-d'),
                                 'selected_cost' => $booking->total_cost
                             ]);
                             if (!$bookingsStylistsTable->save($bookingStylist)) {
                                  \Cake\Log\Log::error('[Admin] Failed to save booking stylist record for stylist ' . $stylistId . ' Errors: ' . json_encode($bookingStylist->getErrors()));
                                 // Use a different flash message for admin
                                 $this->Flash->error(__('The booking was created, but some stylist assignment details could not be saved.'));
                             }
                             $processedStylists[] = $stylistId;
                         }
                     }
                 }

                // Use a different success message for admin
                $this->Flash->success(__('Booking for {0} has been saved successfully.', $customer->first_name . ' ' . $customer->last_name));
                return $this->redirect(['action' => 'index']);
            }
            // Log the errors if the initial save fails
            \Cake\Log\Log::error('[Admin] Failed to save initial booking. Errors: ' . json_encode($booking->getErrors()));
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }

        // Pass variables needed for the form
        $customers = $this->Bookings->Customers->find('list', ['limit' => 200])->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'customers', 'services'));
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
        $closingTime = new \DateTime('17:00'); 

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
     * Private helper to calculate available start time slots for a single service/stylist/date.
     *
     * @param string $date (Y-m-d)
     * @param int $serviceId
     * @param int $stylistId
     * @return array List of available start time strings (H:i format), or empty array on error/no slots.
     */
    private function _calculateAvailableSlots(string $date, int $serviceId, int $stylistId): array
    {
        try {
            // Fetch service details (duration)
            $service = $this->Services->get($serviceId);
            $duration = $service->duration_minutes;
            if ($duration <= 0) {
                $this->log("Service ID {$serviceId} has zero or negative duration.", 'warning');
                return [];
            }

            $availableSlots = [];
            $startHour = 9;
            $endHour = 17; // 5 PM
            $interval = 15; // minutes
            $closingTime = new \DateTime($date . ' 17:00:00');

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                for ($minute = 0; $minute < 60; $minute += $interval) {
                    $slotStartString = sprintf('%02d:%02d:00', $hour, $minute);
                    $potentialStartTime = new \DateTime($date . ' ' . $slotStartString);

                    $potentialEndTime = clone $potentialStartTime;
                    $potentialEndTime->modify("+{$duration} minutes");

                    // Check if the service finishes by closing time
                    if ($potentialEndTime > $closingTime) {
                        // This slot is too late, no need to check further for this hour
                         $this->log("Slot {$slotStartString} for Svc {$serviceId} skipped: ends after closing time.", 'debug');
                        break; // Break inner loop (minutes) for this hour
                    }

                    // Check availability using the existing helper
                    $isSegmentAvailable = $this->checkSegmentAvailability(
                        $date,
                        $potentialStartTime->format('H:i:s'),
                        $potentialEndTime->format('H:i:s'),
                        $serviceId,
                        $stylistId
                    );

                    if ($isSegmentAvailable) {
                         // Check if the start time is in the past (only for today)
                         $todayStr = (new \DateTime())->format('Y-m-d');
                         $now = new \DateTime();
                         if ($date === $todayStr && $potentialStartTime < $now) {
                              $this->log("Slot {$slotStartString} for Svc {$serviceId} skipped: time is in the past.", 'debug');
                             continue; // Skip past time slots
                         }
                        
                        $availableSlots[] = $potentialStartTime->format('H:i'); // Store as H:i
                    }
                } // End minute loop
            } // End hour loop

            return $availableSlots;

        } catch (\Cake\Datasource\Exception\RecordNotFoundException $e) {
            $this->log("Error in _calculateAvailableSlots: Service ID {$serviceId} not found.", 'error');
            return [];
        } catch (\Throwable $e) {
            $this->log('Error in _calculateAvailableSlots: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), 'error');
            return [];
        }
    }

    /**
     * Get Available Time Slots method
     *
     * Handles sequential service bookings with potentially different stylists.
     * Expects input format: { date: 'YYYY-MM-DD', selected_services: [{service_id: X, stylist_id: Y|'any'}, ...] }
     *
     * @return \Cake\Http\Response|null|void Returns JSON response with available start times
     */
    public function getAvailableTimeSlots()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('json');

        $data = $this->request->getData();
        $date = $data['date'] ?? null;
        // Expecting structure like: [{service_id: 1, stylist_id: 5}, {service_id: 2, stylist_id: 'any'}]
        $selectedServiceInput = $data['selected_services'][0] ?? null;

        if (!$date || !$selectedServiceInput || !isset($selectedServiceInput['service_id']) || !isset($selectedServiceInput['stylist_id'])) {
            $this->log('getAvailableTimeSlots: Invalid input data received: ' . json_encode($data), 'warning');
            return $this->response->withStatus(400)->withStringBody(json_encode(['error' => 'Missing or invalid date, service_id, or stylist_id.']));
        }

        try {
            $serviceId = (int)$selectedServiceInput['service_id'];
            $stylistId = (int)$selectedServiceInput['stylist_id'];

            // Call the refactored helper method
            $availableSlotsHi = $this->_calculateAvailableSlots($date, $serviceId, $stylistId);

            // If no slots were found by the helper, return empty array immediately
            if (empty($availableSlotsHi)) {
                return $this->response->withStringBody(json_encode([]));
            }

            // Format the H:i slots into the required value/text format
            $formattedSlots = [];
            foreach ($availableSlotsHi as $slotHi) {
                try {
                    // Use FrozenTime for date-agnostic time parsing/formatting
                    $timeObj = \Cake\I18n\FrozenTime::createFromFormat('H:i', $slotHi);
                    if ($timeObj) {
                        $formattedSlots[] = [
                            'value' => $slotHi, // H:i format
                            'text' => $timeObj->format('h:i A') // h:i A format
                        ];
                    } else {
                        // Log if parsing somehow fails despite valid format expected
                        $this->log("Failed to parse time slot '{$slotHi}' in getAvailableTimeSlots even after calculation.", 'warning');
                    }
                } catch (\Exception $e) {
                     $this->log("Exception parsing time slot '{$slotHi}' in getAvailableTimeSlots: " . $e->getMessage(), 'error');
                }
            }

            return $this->response->withStringBody(json_encode($formattedSlots));

        } catch (\Throwable $e) {
            // Log detailed error
            $this->log('Error in getAvailableTimeSlots (single): ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), 'error');
            // Return generic error
            return $this->response->withStatus(500)->withStringBody(json_encode(['error' => 'An internal error occurred while fetching time slots.']));
        }
    }

    /**
     * Helper function to check if a specific service segment is available for a SPECIFIC stylist.
     *
     * @param string $date Date string (Y-m-d)
     * @param string $startTimeString Start time (H:i:s)
     * @param string $endTimeString End time (H:i:s)
     * @param int $serviceId Service ID
     * @param int $stylistId Specific Stylist ID
     * @return bool True if available, false otherwise
     */
    private function checkSegmentAvailability(string $date, string $startTimeString, string $endTimeString, int $serviceId, int $stylistId): bool
    {
        try {
            $this->log("checkSegmentAvailability: Checking Svc={$serviceId}, Stylist={$stylistId}, Date={$date}, Time={$startTimeString}-{$endTimeString}", 'debug');

            // 1. Check qualification (remains the same)
            $this->log("checkSegmentAvailability: Checking qualification...", 'debug');
            $isQualified = $this->Stylists->find()
                ->where(['Stylists.id' => $stylistId])
                ->matching('Services', function ($q) use ($serviceId) {
                    return $q->where(['Services.id' => $serviceId]);
                })
                ->count() > 0;

            $this->log("checkSegmentAvailability: Qualification result: " . ($isQualified ? 'Qualified' : 'NOT Qualified'), 'debug');
            if (!$isQualified) {
                $this->log("Stylist {$stylistId} not qualified for service {$serviceId}", 'debug');
                return false;
            }

            // 2. Check for booking conflicts by querying BookingsServices
            $this->log("checkSegmentAvailability: Checking for booking conflicts in BookingsServices...", 'debug');
            $bookingsServicesTable = $this->fetchTable('BookingsServices');

            $conflictCount = $bookingsServicesTable->find()
                ->innerJoinWith('Bookings', function ($q) use ($date) {
                    // Also ensure the booking itself isn't cancelled
                    return $q->where([
                        'Bookings.booking_date' => $date,
                        'Bookings.status !=' => 'cancelled' 
                    ]);
                })
                ->where([
                    'BookingsServices.stylist_id' => $stylistId,
                    // Overlap conditions using BookingsServices times
                    'OR' => [
                        // Exact match (unlikely for different services but check anyway)
                        ['BookingsServices.start_time' => $startTimeString, 'BookingsServices.end_time' => $endTimeString],
                        // New slot starts during existing slot
                        ['BookingsServices.start_time <' => $startTimeString, 'BookingsServices.end_time >' => $startTimeString],
                        // New slot ends during existing slot
                        ['BookingsServices.start_time <' => $endTimeString, 'BookingsServices.end_time >' => $endTimeString],
                        // New slot completely contains existing slot
                        ['BookingsServices.start_time >=' => $startTimeString, 'BookingsServices.end_time <=' => $endTimeString],
                         // Existing slot completely contains new slot (Redundant with above checks but safe)
                         // ['BookingsServices.start_time <=' => $startTimeString, 'BookingsServices.end_time >=' => $endTimeString],
                    ],
                    'BookingsServices.start_time IS NOT NULL',
                    'BookingsServices.end_time IS NOT NULL'
                ])
                ->count();

            $hasConflict = $conflictCount > 0;
            $this->log("checkSegmentAvailability: Conflict check result: " . ($hasConflict ? 'Conflict Found ({$conflictCount})' : 'No Conflict'), 'debug');

            return !$hasConflict; // Return true if NO conflict

        } catch (\Throwable $e) {
            $this->log('Error in checkSegmentAvailability: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), 'error');
            return false; // Assume unavailable on error
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

    /**
     * Delete All Past Bookings method
     *
     * @return \Cake\Http\Response|null Redirects to adminPastBookings
     */
    public function deleteAllPastBookings()
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        // Only delete bookings with status finished or cancelled
        $deletedCount = $this->Bookings->deleteAll(['status IN' => ['finished', 'cancelled']]);

        if ($deletedCount) {
            $this->Flash->success(__('{0} past booking(s) have been deleted.', $deletedCount));
        } else {
            $this->Flash->error(__('No past bookings to delete.'));
        }

        return $this->redirect(['action' => 'adminPastBookings']);
    }

    /**
     * Get Stylists for a specific service method
     *
     * Returns a list of stylists qualified to perform the given service.
     *
     * @param int|null $serviceId Service ID
     * @return \Cake\Http\Response|null|void Returns JSON response with stylists
     */
    public function getStylistsForService($serviceId = null)
    {
        // Ensure serviceId is provided and numeric
        if ($serviceId === null || !is_numeric($serviceId)) {
            return $this->response->withStatus(400)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Valid Service ID is required.']));
        }
        $serviceId = (int)$serviceId;

        // Check if the service actually exists
        if (!$this->Services->exists(['id' => $serviceId])) {
             return $this->response->withStatus(404)
                ->withType('application/json')
                ->withStringBody(json_encode(['error' => 'Service not found.']));
        }

        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');

        try {
            $stylists = $this->Stylists->find()
                ->select([
                    'Stylists.id',
                    'Stylists.first_name',
                    'Stylists.last_name'
                ])
                ->distinct(['Stylists.id'])
                ->innerJoinWith('Services', function ($q) use ($serviceId) {
                    return $q->where(['Services.id' => $serviceId]);
                })
                ->order([
                    'Stylists.first_name' => 'ASC',
                    'Stylists.last_name' => 'ASC'
                ])
                ->toArray();

            // Format the results for easy use in a dropdown/select list
            $formattedStylists = array_map(function ($stylist) {
                return [
                    'id' => $stylist->id,
                    'name' => $stylist->first_name . ' ' . $stylist->last_name
                ];
            }, $stylists);

            return $this->response->withStringBody(json_encode($formattedStylists));

        } catch (\Throwable $e) {
            $this->log('Error in getStylistsForService: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return $this->response->withStatus(500)
                ->withStringBody(json_encode(['error' => 'An internal error occurred while fetching stylists for the service.']));
        }
    }

    /**
     * Get the count of available time slots for a specific service, stylist, and date.
     * Handles AJAX requests from the booking form.
     */
    public function getAvailabilityCount()
    {
        $this->request->allowMethod(['get']);
        // Ensure JSON view is set up early
        $this->viewBuilder()->setClassName('Json');
        // Default response status
        $this->response = $this->response->withStatus(200);

        $serviceId = $this->request->getQuery('service_id');
        $stylistId = $this->request->getQuery('stylist_id');
        $date = $this->request->getQuery('date');

        // Use array for response data
        $responseData = [];

        if (!$serviceId || !$stylistId || !$date) {
            $this->response = $this->response->withStatus(400); 
            $responseData['error'] = 'Missing required parameters (service_id, stylist_id, date).';
        } else {
            try {
                // Basic validation/parsing
                $serviceId = (int)$serviceId;
                $stylistId = (int)$stylistId;
                // Add validation for date format if needed
                // Example: \Cake\I18n\Date::parseDate($date, 'yyyy-MM-dd');

                // --- Use the refactored logic to get actual slots ---
                $availableSlots = $this->_calculateAvailableSlots($date, $serviceId, $stylistId);
                $availableSlotsCount = count($availableSlots);

                $responseData['availableSlotsCount'] = $availableSlotsCount;

            } catch (\Exception $e) {
                // Log the detailed error on the server
                \Cake\Log\Log::error('Error in getAvailabilityCount: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
                
                $this->response = $this->response->withStatus(500); // Internal Server Error
                $responseData['error'] = 'An internal error occurred while checking availability.';
            }
        }

        // Set the response data and serialization keys
        $this->set($responseData);
        $this->viewBuilder()->setOption('serialize', array_keys($responseData));
    }
}
