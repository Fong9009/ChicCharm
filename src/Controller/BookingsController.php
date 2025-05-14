<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Table\BookingsStylistsTable;
use App\Model\Table\ServicesTable;
use App\Model\Table\StylistsTable;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\DateTime as CakeDateTime;
use Cake\I18n\FrozenTime;
use Cake\I18n\FrozenDate;
use Cake\Log\Log;
use Cake\View\Exception\MissingTemplateException;
use DateTime;
use Exception;
use Throwable;
use Cake\ORM\TableRegistry;
use Cake\Core\Configure;
use Mpdf\Mpdf; // <<< ADDED
use Cake\View\View; // <<< ADDED

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

    /**
     * @return void
     * @throws \Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Services = $this->getTableLocator()->get('Services');
        $this->Stylists = $this->getTableLocator()->get('Stylists');
        $this->BookingsStylists = $this->getTableLocator()->get('BookingsStylists');
        $this->loadComponent('Recaptcha.Recaptcha');
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['guestbooking']);
        // Allow unauthenticated access to the booking route
        $this->Authentication->addUnauthenticatedActions(['booking']);
    }

    /**
     * @param EventInterface $event
     * @return Response|void|null
     */
    public function beforeFilter(EventInterface $event)
    {
        parent::beforeFilter($event);

        // Get the current user
        $user = $this->Authentication->getIdentity();

        // Define customer-specific actions
        $customerActions = ['customerbooking', 'customerindex', 'customerview', 'dashboard', 'customeredit'];

        $stylistActions = ['stylistindex', 'stylistview'];

        // Define admin-specific actions
        $adminActions = ['adminbooking', 'edit', 'index', 'stylistedit', 'view'];

        // If the current action is a customer action
        if (in_array($this->request->getParam('action'), $customerActions)) {
            // Check if user is logged in and is a customer
            if (!$user || $user->type !== 'customer') {
                $this->Flash->error('Access denied. This area is for customers only.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'landing']);
            }
        }

        // If the current action is an admin action
        if (in_array($this->request->getParam('action'), $adminActions)) {
            // Check if user is logged in and is an admin
            if (!$user || $user->type !== 'admin') {
                $this->Flash->error('Access denied. This area is for administrators only.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'landing']);
            }
        }

        // If the current action is a customer action
        if (in_array($this->request->getParam('action'), $stylistActions)) {
            // Check if user is logged in and is a customer
            if (!$user || $user->type !== 'stylist') {
                $this->Flash->error('Access denied. This area is for Stylists only.');

                return $this->redirect(['controller' => 'Pages', 'action' => 'display', 'landing']);
            }
        }

        // Automatically update booking statuses for past bookings
        $now = new CakeDateTime();
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
            if (empty($booking->bookings_services)) {
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
                    $currentServiceEndTime = FrozenTime::parse($bs->end_time->format('H:i:s'));
                    if ($latestEndTime === null || $currentServiceEndTime > $latestEndTime) {
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
                'Bookings.remaining_cost',
                'Bookings.refund_due_amount',
                'Bookings.status',
                'Bookings.notes',
            ])
            ->where(['status IN' => ['Confirmed - Payment Due', 'Confirmed - Paid']])
            ->contain([
                'Customers' => [
                    'fields' => ['id', 'first_name', 'last_name'],
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost'],
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'PaymentHistories' => function ($q) {
                    return $q->select([
                        'PaymentHistories.booking_id',
                        'PaymentHistories.payment_method',
                        'PaymentHistories.payment_status'
                    ])
                               ->orderBy(['PaymentHistories.payment_date' => 'DESC']);
                }
            ]);

        // Search functionality
        $search = $this->request->getQuery('search');
        if ($search) {
            $query->where([
                'OR' => [
                    'Bookings.booking_name LIKE' => '%' . $search . '%',
                    'Customers.first_name LIKE' => '%' . $search . '%',
                    'Customers.last_name LIKE' => '%' . $search . '%',
                ]
            ]);
        }

        // Filter by status (only allow Confirmed - Payment Due and Confirmed - Paid)
        $filter = $this->request->getQuery('filter');
        if ($filter && in_array($filter, ['Confirmed - Payment Due', 'Confirmed - Paid'])) {
            $query->where(['Bookings.status' => $filter]);
        }

        $query->orderBy(['booking_date' => 'ASC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
    }

    /**
     * Customer Index Where they can see all their current bookings
     *
     * @return void
     */
    public function customerindex(): void
    {
        $today = FrozenDate::today();
        $query = $this->Bookings->find()
            ->select([
                'Bookings.id',
                'Bookings.customer_id',
                'Bookings.booking_name',
                'Bookings.booking_date',
                'Bookings.total_cost',
                'Bookings.remaining_cost',
                'Bookings.status',
                'Bookings.refund_due_amount'
            ])
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
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'PaymentHistories' => function ($q) {
                    return $q->select([
                        'PaymentHistories.booking_id',
                        'PaymentHistories.invoice_pdf',
                        'PaymentHistories.payment_date',
                        'PaymentHistories.payment_method',
                        'PaymentHistories.payment_status'
                    ])
                               ->orderBy(['PaymentHistories.payment_date' => 'DESC']);
                }
            ])
            ->orderBy([
                'ABS(DATEDIFF(booking_date, CURDATE()))' => 'ASC',
                'booking_date' => 'ASC',
            ]);

        $bookings = $this->paginate($query);

        foreach ($bookings as $booking) {
            if (!empty($booking->payment_histories)) {
                $booking->latest_payment_history = $booking->payment_histories[0];
            } else {
                $booking->latest_payment_history = null;
            }
        }

        $this->set(compact('bookings'));
    }

    /**
     * Stylist index for the stylist so they can see who has booked them
     *
     * @return void
     */
    public function stylistindex(): void
    {
        $stylist = $this->Stylists->get($this->Authentication->getIdentity()->id);

        $today = FrozenDate::today();
        //Bookings that have the selected Stylist
        $bookingsTable = $this->fetchTable('Bookings');
        $query = $bookingsTable->find()
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
            ->where([
                'Bookings.status IN' => ['finished', 'cancelled','Confirmed - Paid'],
                'Bookings.booking_date >=' => $today
            ])
            ->orderBy([
                'ABS(DATEDIFF(booking_date, CURDATE()))' => 'ASC',
                'booking_date' => 'ASC',
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

        $booking = $this->Bookings->get($id, contain: [
            'Customers',
            'BookingsServices.Services',
            'BookingsStylists.Stylists',
        ]);
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
                        'selected_cost' => $booking->total_cost,
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

        $numberHelper = new \Cake\View\Helper\NumberHelper(new \Cake\View\View());

        $booking = $this->Bookings->get($id, contain: [
            'Customers',
            'BookingsStylists' => ['Stylists'],
            'BookingsServices' => ['Services', 'Stylists'],
        ]);

        $originalTotalCost = $booking->total_cost;
        $originalRemainingCost = $booking->remaining_cost;
        $originalStatus = $booking->status;

        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $bookingId = $booking->id;

            if (isset($data['booking_date'])) {
                $data['booking_date_formatted'] = $data['booking_date'];
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

            $totalPreviousCost = 0;

            if ($booking->status === 'Confirmed - Paid') {
                //Already paid, comparison on what was already charged
                $bookingServicesTable = TableRegistry::getTableLocator()->get('BookingsServices');
                $services = $bookingServicesTable->find()
                    ->where(['booking_id' => $bookingId])
                    ->all();

                foreach ($services as $service) {
                    $totalPreviousCost += $service->service_cost;
                }

                if ($totalCost < $totalPreviousCost) {
                    //Refund the difference
                    $refund = $totalPreviousCost - $totalCost;
                    $data['remaining_cost'] = 0;
                    $data['refund_due_amount'] = $refund; // Set refund_due_amount
                } elseif ($totalCost > $totalPreviousCost) {
                    $data['remaining_cost'] = $totalCost - $totalPreviousCost;
                    $data['refund_due_amount'] = 0; // Reset if now owes more
                    $booking->status = 'Confirmed - Payment Due';
                } else {
                    $data['remaining_cost'] = 0;
                    $data['refund_due_amount'] = 0; // Reset if cost is same
                    $this->Flash->success(__('Cost is the same, no change required'));
                }

            } elseif ($booking->status === 'Confirmed - Payment Due') {
                //Compare the new total with the sum of (total originally owed - remaining unpaid)
                $paidSoFar = $booking->total_cost - $booking->remaining_cost;

                if ($totalCost < $paidSoFar) {
                    $refund = $paidSoFar - $totalCost;
                    $data['remaining_cost'] = 0;
                    $data['refund_due_amount'] = $refund; // Set refund_due_amount
                    $booking->status = 'Confirmed - Paid';
                    $this->Flash->info(__('A refund of {0} is due to the customer as their new total is less than what they have already paid. Please process this manually via PayPal.', $numberHelper->currency($refund, 'AUD')));
                } elseif ($totalCost > $paidSoFar) {
                    $data['remaining_cost'] = $totalCost - $paidSoFar;
                    $data['refund_due_amount'] = 0; // Reset if still owes more or owes different amount
                } else {
                    $data['remaining_cost'] = 0;
                    $data['refund_due_amount'] = 0; // Reset if paid exactly
                    $booking->status = 'Confirmed - Paid';
                }
            } else {
                // For new or unconfirmed bookings
                // If all else fails
                $data['remaining_cost'] = $totalCost;
                $data['refund_due_amount'] = 0; // Ensure it's 0 for new/active bookings
            }

            $data['total_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? null;

            // Check if any service data was submitted (might be empty if all services unchecked)
            $hasServiceData = !empty($data['bookings_services']);

            // Start Transaction
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
                    'refund_due_amount' => $data['refund_due_amount'], // Add to patch data
                    'notes' => $data['notes'],
                 ], ['associated' => []]);

                // Save main booking changes
                if (!$this->Bookings->save($booking)) {
                    throw new Exception('Failed to save main booking updates.');
                }

                $allServicesTimes = [];
                // Fetch service durations if we have services
                $servicesDetails = [];
                if ($hasServiceData) {
                    $serviceIds = array_column($data['bookings_services'], 'service_id');
                    if (!empty($serviceIds)) {
                        $servicesDetails = $this->Services->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'duration_minutes',
                        ])->where(['id IN' => $serviceIds])->toArray();
                    }

                    $stylistTimeSlotsEdit = [];
                    $hasConflictEdit = false;
                    $conflictMessageEdit = '';

                    foreach ($data['bookings_services'] as $serviceData) {
                        if (!isset($serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_id'])) {
                            continue;
                        }
                        $stylistIdEdit = (int)$serviceData['stylist_id'];
                        $serviceIdEdit = (int)$serviceData['service_id'];
                        $startTimeStrEdit = $serviceData['start_time'];
                        $bookingDateStrEdit = $data['booking_date_formatted'];
                        // Use the $servicesDetails array fetched just above
                        $durationEdit = $servicesDetails[$serviceIdEdit] ?? 0;

                        if ($durationEdit <= 0) continue;

                        try {
                             $startTimeEdit = new DateTime($bookingDateStrEdit . ' ' . $startTimeStrEdit);
                             $endTimeEdit = clone $startTimeEdit;
                             $endTimeEdit->modify("+{$durationEdit} minutes");
                             $newSlotEdit = ['start' => $startTimeEdit->getTimestamp(), 'end' => $endTimeEdit->getTimestamp(), 'service_id' => $serviceIdEdit];

                            if (isset($stylistTimeSlotsEdit[$stylistIdEdit])) {
                                foreach ($stylistTimeSlotsEdit[$stylistIdEdit] as $existingSlotEdit) {
                                    if ($newSlotEdit['start'] < $existingSlotEdit['end'] && $newSlotEdit['end'] > $existingSlotEdit['start']) {
                                        $hasConflictEdit = true;
                                        $conflictMessageEdit = "Time conflict detected for one of the selected stylists.
                                         Please ensure service times do not overlap.";
                                        break 2;
                                    }
                                }
                            }
                             $stylistTimeSlotsEdit[$stylistIdEdit][] = $newSlotEdit;
                        } catch (Exception $e) {
                            // Throw exception to trigger transaction rollback
                             throw new Exception('Validation time processing error: ' . $e->getMessage());
                        }
                    }

                    if ($hasConflictEdit) {
                        // Throw exception to trigger transaction rollback
                        throw new Exception($conflictMessageEdit ?: 'A time conflict was detected. Please ensure service times for the same stylist do not overlap.');
                    }

                    // Re-Save BookingsServices records with individual start/end times
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            Log::warning('[Edit] Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }
                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;
                        if ($duration <= 0) {
                            Log::warning("[Edit] Skipping service ID {$serviceId} with zero duration.");
                            continue;
                        }
                        $startTime = new DateTime($data['booking_date_formatted'] . ' ' . $startTimeString);
                        $endTime = clone $startTime;
                        $endTime->modify("+{$duration} minutes");

                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $bookingId,
                            'service_id' => $serviceId,
                            'stylist_id' => (int)$serviceData['stylist_id'],
                            'start_time' => $startTime->format('H:i:s'),
                            'end_time' => $endTime->format('H:i:s'),
                            'service_cost' => $serviceData['service_cost'],
                        ]);
                        if (!$bookingsServicesTable->save($bookingService)) {
                            // Rollback on failure
                            throw new Exception('Failed to save updated booking service details.');
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
                     throw new Exception('Failed to save overall times on booking update.');
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
                                 'selected_cost' => $booking->total_cost,
                            ]);
                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                 throw new Exception('Failed to save updated booking stylist details.');
                            }
                             $processedStylists[] = $stylistId;
                        }
                    }
                }

                // Commit transaction
                $connection->commit();
                $this->Flash->success(__('The booking has been updated successfully.'));

                $newTotalCost = $booking->total_cost;
                $newRemainingCost = $booking->remaining_cost;
                $newStatus = $booking->status;
                $customerEmail = $booking->customer->email;

                $emailDetails = null;

                if ($originalStatus === 'Confirmed - Paid') {
                    if ($newTotalCost < $originalTotalCost) {
                        $refundAmount = $originalTotalCost - $newTotalCost;
                        $emailDetails = [
                            'type' => 'refund_due',
                            'amount' => $refundAmount,
                            'original_total' => $originalTotalCost,
                            'paid_so_far' => $originalTotalCost,
                            'new_total' => $newTotalCost,
                            'customer_email' => $customerEmail
                        ];
                    } elseif ($newTotalCost > $originalTotalCost) {
                        $additionalAmount = $newTotalCost - $originalTotalCost;
                        $emailDetails = [
                            'type' => 'additional_payment_due',
                            'amount' => $additionalAmount,
                            'original_total' => $originalTotalCost,
                            'paid_so_far' => $originalTotalCost,
                            'new_total' => $newTotalCost,
                            'customer_email' => $customerEmail
                        ];
                    }
                } elseif ($originalStatus === 'Confirmed - Payment Due') {
                    $paidSoFar = $originalTotalCost - $originalRemainingCost;
                    if ($newTotalCost < $paidSoFar) {
                        $refundAmount = $paidSoFar - $newTotalCost;
                        $emailDetails = [
                            'type' => 'refund_due',
                            'amount' => $refundAmount,
                            'original_total' => $originalTotalCost,
                            'paid_so_far' => $paidSoFar,
                            'new_total' => $newTotalCost,
                            'customer_email' => $customerEmail
                        ];
                    } elseif ($newTotalCost > $paidSoFar) { 
                        $emailDetails = [
                            'type' => 'additional_payment_due',
                            'amount' => $newRemainingCost,
                            'original_total' => $originalTotalCost,
                            'paid_so_far' => $paidSoFar,
                            'new_total' => $newTotalCost,
                            'customer_email' => $customerEmail
                        ];
                    }
                }

                // Set conditional flash messages 
                if ($emailDetails) {
                    if ($emailDetails['type'] === 'refund_due') {
                        $this->Flash->info(__(
                            'A refund of {0} is due to the customer. Ensure this is processed. The customer will be notified.',
                            $numberHelper->currency($emailDetails['amount'], 'AUD')
                        ));
                    } elseif ($emailDetails['type'] === 'additional_payment_due') {
                        $this->Flash->info(__(
                            'An additional payment of {0} is now due. The customer will be notified.',
                            $numberHelper->currency($emailDetails['amount'], 'AUD')
                        ));
                    }
                } else {
                    $this->Flash->success(__('The booking has been updated successfully.'));
                }

                if ($emailDetails) {
                    Log::info('[AdminEditNotification] Prepared email details: ' . json_encode($emailDetails), ['scope' => ['admin_edit_email']]);

                    $newPaymentHistoryForEdit = null;
                    $paymentHistoriesTable = TableRegistry::getTableLocator()->get('PaymentHistories');

                    if ($emailDetails['type'] === 'additional_payment_due') {
                        $newPaymentHistoryForEdit = $paymentHistoriesTable->newEntity([
                            'booking_id' => $booking->id,
                            'customer_id' => $booking->customer_id,
                            'payment_amount' => $emailDetails['amount'],
                            'payment_currency' => 'AUD',
                            'payment_status' => 'Pending - Admin Processing',
                            'payment_method' => 'Admin Adjustment',
                            'payment_date' => FrozenTime::now(),
                            'notes' => 'Additional charge due to booking modification by admin. Original Total: ' . $numberHelper->currency($emailDetails['original_total'], 'AUD') . ', Paid So Far: ' . $numberHelper->currency($emailDetails['paid_so_far'] ?? 0, 'AUD') . ', New Total: ' . $numberHelper->currency($emailDetails['new_total'], 'AUD') . '.'
                        ]);
                    } elseif ($emailDetails['type'] === 'refund_due') {
                        $newPaymentHistoryForEdit = $paymentHistoriesTable->newEntity([
                            'booking_id' => $booking->id,
                            'customer_id' => $booking->customer_id,
                            'payment_amount' => $emailDetails['amount'],
                            'payment_currency' => 'AUD',
                            'payment_status' => 'Refunded - Admin Processed',
                            'payment_method' => 'Admin Adjustment',
                            'payment_date' => FrozenTime::now(),
                            'notes' => 'Refund due to booking modification by admin. Original Total: ' . $numberHelper->currency($emailDetails['original_total'], 'AUD') . ', Paid So Far: ' . $numberHelper->currency($emailDetails['paid_so_far'] ?? $emailDetails['original_total'], 'AUD') . ', New Total: ' . $numberHelper->currency($emailDetails['new_total'], 'AUD') . '. Refund Amount: ' . $numberHelper->currency($emailDetails['amount'], 'AUD') . '.'
                        ]);
                    }

                    if ($newPaymentHistoryForEdit) {
                        if ($paymentHistoriesTable->save($newPaymentHistoryForEdit)) {
                            Log::info("[AdminEditNotification] Created new PaymentHistory ID: {$newPaymentHistoryForEdit->id} for booking ID: {$booking->id}, Type: {$emailDetails['type']}");

                            // Generate and save PDF
                            try {
                                // Fetch the latest ACTUAL payment history before this edit, if any
                                $originalPaymentHistory = $paymentHistoriesTable->find()
                                    ->where([
                                        'booking_id' => $booking->id,
                                        'id !=' => $newPaymentHistoryForEdit->id,
                                    ])
                                    ->orderBy(['payment_date' => 'DESC'])
                                    ->first();

                                $pdfView = new View();
                                $pdfView->set([
                                    // 'booking' will be set below with full details
                                    'paymentHistory' => $originalPaymentHistory,
                                    'editedPaymentHistory' => $newPaymentHistoryForEdit,
                                    'companyName' => Configure::read('MyApp.companyName', 'ChicCharm'),
                                    'companyAddress' => Configure::read('MyApp.companyAddress', '123 Beauty Lane, Styleville'),
                                    'companyPhone' => Configure::read('MyApp.companyPhone', '03 9000 0000'),
                                    'companyEmail' => Configure::read('MyApp.companyEmail', 'contact@chiccharm.com'),
                                    'companyABN' => Configure::read('MyApp.companyABN', '12 345 678 910'),
                                    'isPdfContext' => true,
                                    'isAdminEditNotification' => true,
                                    'changeDetails' => $emailDetails,
                                ]);
                                // Ensure the booking entity passed to the template has all necessary associations
                                $bookingWithDetailsForPdf = $this->Bookings->get($booking->id, [
                                    'contain' => ['Customers', 'BookingsServices.Services', 'BookingsServices.Stylists', 'BookingsStylists.Stylists']
                                ]);
                                $pdfView->set('booking', $bookingWithDetailsForPdf);


                                $html = $pdfView->render('email/html/invoice', false);
                                $mpdf = new Mpdf(['tempDir' => TMP . 'mpdf']);
                                $mpdf->WriteHTML($html);

                                $pdfDir = WWW_ROOT . 'invoices' . DS;
                                if (!is_dir($pdfDir)) {
                                    mkdir($pdfDir, 0775, true);
                                }
                                $pdfFileName = 'invoice_edit_' . $booking->id . '_' . $newPaymentHistoryForEdit->id . '.pdf';
                                $pdfPath = 'invoices/' . $pdfFileName;
                                $fullPdfPath = WWW_ROOT . $pdfPath;
                                $mpdf->Output($fullPdfPath, \Mpdf\Output\Destination::FILE);

                                $newPaymentHistoryForEdit->invoice_pdf = $pdfPath;
                                if ($paymentHistoriesTable->save($newPaymentHistoryForEdit)) {
                                    Log::info("[AdminEditNotification] Successfully generated and saved PDF {$pdfPath} for PaymentHistory ID: {$newPaymentHistoryForEdit->id}");
                                } else {
                                    Log::error("[AdminEditNotification] Failed to save invoice_pdf path for PaymentHistory ID: {$newPaymentHistoryForEdit->id}. Errors: " . json_encode($newPaymentHistoryForEdit->getErrors()));
                                }
                            } catch (\Exception $pdfException) {
                                Log::error("[AdminEditNotification] Failed to generate PDF for PaymentHistory ID: {$newPaymentHistoryForEdit->id}. Error: " . $pdfException->getMessage() . "\nStack Trace:\n" . $pdfException->getTraceAsString());
                            }
                        } else {
                            Log::error("[AdminEditNotification] Failed to save new PaymentHistory for booking ID: {$booking->id}. Errors: " . json_encode($newPaymentHistoryForEdit->getErrors()));
                            $newPaymentHistoryForEdit = null;
                        }
                    }

                    try {
                        // Ensure Customer entity is loaded with the booking for the mailer
                        $bookingWithCustomer = $this->Bookings->get($booking->id, ['contain' => ['Customers', 'PaymentHistories' => function($q){ return $q->orderBy(['PaymentHistories.payment_date' => 'DESC']);} ]]);
                        if ($bookingWithCustomer && $bookingWithCustomer->customer && $bookingWithCustomer->customer->email) {
                            $mailer = new \App\Mailer\InvoiceMailer();
                            $mailer->sendAdminEditNotification($bookingWithCustomer, $emailDetails, $newPaymentHistoryForEdit);
                            Log::info('[AdminEditNotification] Mailer method called successfully for booking ID: ' . $booking->id, ['scope' => ['admin_edit_email']]);
                        } else {
                            Log::error('[AdminEditNotification] Could not send email: Customer data or email missing for booking ID: ' . $booking->id, ['scope' => ['admin_edit_email']]);
                        }
                    } catch (\Exception $e) {
                        Log::error('[AdminEditNotification] Error sending email for booking ID: ' . $booking->id . '. Error: ' . $e->getMessage(), ['scope' => ['admin_edit_email']]);
                    }
                }

                return $this->redirect(['action' => 'index']);
            } catch (Exception $e) {
                $connection->rollback();
                Log::error('[Edit] Booking update failed: '
                    . $e->getMessage() .
                    ' Booking ID: '
                    . $bookingId
                    . ' Data: '
                    . json_encode($data));
                $this->Flash->error(__('The booking could not be updated. Please, try again. Error: {0}', $e->getMessage()));
                 $booking->setErrors(json_decode($e->getMessage(), true) ?: []);
            }
        }

        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'stylists', 'services', 'customers'));
    }

    public function editStatus($id = null)
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');

            return $this->redirect(['action' => 'customerindex']);
        }

        $booking = $this->Bookings->get($id);
        $booking->status = 'Confirmed - Paid';
        $booking->remaining_cost = 0;
        if ($this->Bookings->save($booking)) {
            $customerName = $booking->has('customer') && $booking->customer ? $booking->customer->first_name . ' ' . $booking->customer->last_name : 'the customer';
            $this->Flash->success(__('Booking #{0} for {1} has been marked as paid in store.', $booking->id, $customerName));
        } else {
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    /**
     * @param $stylistId
     * @param $bookingId
     * @return Response|null
     */
    public function removeStylist($stylistId, $bookingId)
    {
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
                'booking_id' => $bookingId,
            ])->first();

        //Obtains the booking Stylist from the booking Stylist table
        $bookingStylist = $bookStylistTable
            ->find()
            ->where([
                'stylist_id' => $stylistId,
                'booking_id' => $bookingId,
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
                if ($bookServicesTable->delete($bookingService) && $bookStylistTable->delete($bookingStylist)) {
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
            $this->Flash->error(__('Access denied. Admin only area.'));

            return $this->redirect(['action' => 'customerindex']);
        }

        $this->request->allowMethod(['post', 'delete']);
        $booking = $this->Bookings->get($id, ['contain' => ['Customers']]);
        $originalStatus = $booking->status;
        $originalTotalCost = $booking->total_cost;

        // Change status to 'cancelled'
        $booking->status = 'cancelled';

        // If original status was 'Confirmed - Paid', set refund_due_amount
        if ($originalStatus === 'Confirmed - Paid') {
            $booking->refund_due_amount = $originalTotalCost;
        }

        if ($this->Bookings->save($booking)) {
            $numberHelper = new \Cake\View\Helper\NumberHelper(new \Cake\View\View());  
            // Conditional Flash Message
            if ($originalStatus === 'Confirmed - Paid') {
                $this->Flash->success(__('The booking has been cancelled. Refund of {0} is being processed.', $numberHelper->currency($originalTotalCost, 'AUD'))); 
            } else {
                $this->Flash->success(__('The booking has been cancelled.'));
            }

            $paymentHistoriesTable = TableRegistry::getTableLocator()->get('PaymentHistories');

            if ($originalStatus === 'Confirmed - Paid') {
                // Create a PaymentHistory record for the refund
                $refundPaymentHistory = $paymentHistoriesTable->newEntity([
                    'booking_id' => $id,
                    'customer_id' => $booking->customer_id,
                    'payment_amount' => $originalTotalCost,
                    'payment_currency' => 'AUD',
                    'payment_status' => 'Refunded - Admin Processed',
                    'payment_method' => 'Admin Cancellation',
                    'payment_date' => FrozenTime::now(),
                    'notes' => 'Full refund due to admin cancellation of a previously paid booking.'
                ]);
                if (!$paymentHistoriesTable->save($refundPaymentHistory)) {
                    Log::error("Admin: Failed to create refund PaymentHistory for cancelled booking ID {$id}. Errors: " . json_encode($refundPaymentHistory->getErrors()));
                    $this->Flash->warning(__('Booking cancelled, but failed to record the refund transaction. Please check payment histories.'));
                } else {
                    Log::info("Admin: Created refund PaymentHistory ID {$refundPaymentHistory->id} for cancelled booking ID {$id}.");
                    try {
                        if ($booking->customer && $booking->customer->email) {
                            $mailer = new \App\Mailer\InvoiceMailer();
                            $refundAmountFloat = (float)$originalTotalCost;
                            $mailer->sendAdminCancellationRefundNotification($booking, $refundAmountFloat);
                            Log::info("Admin cancellation/refund email initiated for booking ID {$id} to customer {$booking->customer->email}.");
                        } else {
                            Log::warning("Admin cancellation: Customer email not found for booking ID {$id}, cannot send notification email.");
                        }
                    } catch (\Exception $e) {
                        Log::error("Admin cancellation: Failed to send notification email for booking ID {$id}. Error: " . $e->getMessage());
                    }
                }
            } else {
                // If the booking was not already paid, void any 'Pending' PaymentHistory records
                $pendingPayments = $paymentHistoriesTable->find()
                    ->where([
                        'booking_id' => $id,
                        'payment_status' => 'Pending'
                    ])
                    ->all();

                foreach ($pendingPayments as $payment) {
                    $payment->payment_status = 'Payment Voided';
                    $payment->notes = ($payment->notes ? $payment->notes . ' ' : '') . 'Booking cancelled by admin, payment voided.';
                    if (!$paymentHistoriesTable->save($payment)) {
                        Log::error("Admin: Failed to void PaymentHistory ID {$payment->id} for cancelled booking ID {$id}. Errors: " . json_encode($payment->getErrors()));
                    }
                }

                // Send a cancellation email for non-paid bookings
                try {
                    if ($booking->customer && $booking->customer->email) {
                        $mailer = new \App\Mailer\InvoiceMailer();
                        $mailer->sendAdminCancellationNotification($booking);
                        Log::info("Admin cancellation email (non-paid) initiated for booking ID {$id} to customer {$booking->customer->email}.");
                    } else {
                        Log::warning("Admin cancellation (non-paid): Customer email not found for booking ID {$id}, cannot send notification email.");
                    }
                } catch (\Exception $e) {
                    Log::error("Admin cancellation (non-paid): Failed to send notification email for booking ID {$id}. Error: " . $e->getMessage());
                }
            }
        } else {
            // Log the error for debugging
            Log::error("Admin: Failed to cancel booking ID {$id}. Errors: " . json_encode($booking->getErrors()));
            $this->Flash->error(__('The booking could not be cancelled. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }

    public function customerdelete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $booking = $this->Bookings->get($id);
        $originalStatus = $booking->status;

        if ($booking->status === 'Confirmed - Paid') {
            $this->Flash->error(__('This booking has been paid and can no longer be cancelled.'));
            return $this->redirect($this->getRedirectUrlAfterCustomerAction($id));
        }

        // Change status to 'cancelled'
        $booking->status = 'cancelled';
        if ($this->Bookings->save($booking)) {
            $this->Flash->success(__('The booking has been cancelled.'));

            if ($originalStatus !== 'Confirmed - Paid') {
                $paymentHistoriesTable = TableRegistry::getTableLocator()->get('PaymentHistories');
                $pendingPayments = $paymentHistoriesTable->find()
                    ->where([
                        'booking_id' => $id,
                        'payment_status' => 'Pending'
                    ])
                    ->all();

                foreach ($pendingPayments as $payment) {
                    $payment->payment_status = 'Payment Voided';
                    $payment->notes = ($payment->notes ? $payment->notes . ' ' : '') . 'Booking cancelled by customer, payment voided.';
                    if (!$paymentHistoriesTable->save($payment)) {
                        Log::error("Customer: Failed to void PaymentHistory ID {$payment->id} for cancelled booking ID {$id}. Errors: " . json_encode($payment->getErrors()));
                    }
                }
            }
        } else {
            Log::error("Customer: Failed to cancel booking ID {$id}. Errors: " . json_encode($booking->getErrors()));
            $this->Flash->error(__('The booking could not be cancelled. Please, try again.'));
        }

        return $this->redirect($this->getRedirectUrlAfterCustomerAction($id));
    }

    // Helper method to determine redirect URL
    private function getRedirectUrlAfterCustomerAction($bookingId = null) 
    {
        $referer = $this->request->getHeaderLine('Referer'); 

        if ($bookingId && !empty($referer) && strpos($referer, 'bookings/customerview') !== false) {
            return ['controller' => 'Bookings', 'action' => 'customerview', $bookingId];
        } elseif (!empty($referer) && strpos($referer, 'customers/dashboard') !== false) {
            return ['controller' => 'Customers', 'action' => 'dashboard'];
        }
        return ['controller' => 'Customers', 'action' => 'dashboard']; 
    }

    public function customerbooking()
    {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            Log::debug('[CustomerBooking] POST request received.');
            $data = $this->request->getData();
            Log::debug('[CustomerBooking] Request Data: ' . json_encode($data));

            // Check if end time exceeds 5 PM
            if (isset($data['end_time'])) {
                $endTime = new DateTime($data['end_time']);
                $closingTime = new DateTime('17:00');

                if ($endTime > $closingTime) {
                    $this->Flash->error(__('Booking cannot extend past 5 PM as the shop will be closed.'));

                    return $this->redirect(['action' => 'customerbooking']);
                }
            }

            // Automatically set customer details
            $user = $this->Authentication->getIdentity();
            $data['customer_id'] = $user->id;
            $data['booking_name'] = 'Booking for ' . $user->first_name . ' ' . $user->last_name;

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
                'remaining_cost' => $data['total_cost'],
                'notes' => $data['notes'] ?? null,
                'status' => 'Confirmed - Payment Due',
            ]);

            $stylistTimeSlots = [];
            $hasConflict = false;
            $conflictMessage = '';

            if (!empty($data['bookings_services'])) {
                // Fetch service durations needed for validation
                $serviceIdsForValidation = array_column($data['bookings_services'], 'service_id');
                $servicesDetailsForValidation = [];
                if (!empty($serviceIdsForValidation)) {
                     $servicesDetailsForValidation = $this->fetchTable('Services')->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes',
                     ])->where(['id IN' => $serviceIdsForValidation])->toArray();
                }

                foreach ($data['bookings_services'] as $serviceData) {
                    if (!isset($serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_id'])) {
                         continue;
                    }
                    $stylistId = (int)$serviceData['stylist_id'];
                    $serviceId = (int)$serviceData['service_id'];
                    $startTimeStr = $serviceData['start_time'];
                    $duration = $servicesDetailsForValidation[$serviceId] ?? 0;

                    if ($duration <= 0) continue;

                    try {
                         $startTime = new DateTime($data['booking_date'] . ' ' . $startTimeStr);
                         $endTime = clone $startTime;
                         $endTime->modify("+{$duration} minutes");
                         $newSlot = ['start' => $startTime->getTimestamp(), 'end' => $endTime->getTimestamp(), 'service_id' => $serviceId];

                        if (isset($stylistTimeSlots[$stylistId])) {
                            foreach ($stylistTimeSlots[$stylistId] as $existingSlot) {
                                if ($newSlot['start'] < $existingSlot['end'] && $newSlot['end'] > $existingSlot['start']) {
                                    $hasConflict = true;
                                    $conflictMessage = "Time conflict detected for one of the selected stylists. Please ensure service times do not overlap.";
                                    break 2;
                                }
                            }
                        }
                         $stylistTimeSlots[$stylistId][] = $newSlot;

                    } catch (Exception $e) {
                         Log::error('Validation time processing error: ' . $e->getMessage());
                         $this->Flash->error(__('An error occurred while validating booking times. Please check the selected times.'));
                         $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
                         $services = $this->fetchTable('Services')->find('all')->all();
                         $this->set(compact('booking', 'stylists', 'services'));

                         return $this->render('customerbooking');
                    }
                }
            }

            if ($hasConflict) {
                $this->Flash->error($conflictMessage ?: __('A time conflict was detected. Please ensure service times for the same stylist do not overlap.'));
                $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
                $services = $this->fetchTable('Services')->find('all')->all();
                $booking = $this->Bookings->patchEntity($booking, $data, ['associated' => []]);
                $this->set(compact('booking', 'stylists', 'services'));
                $this->request = $this->request->withParsedBody($data);

                 return $this->render('customerbooking');
            }

            if ($this->Bookings->save($booking)) {
                Log::debug('[CustomerBooking] Initial booking save successful. ID: ' . $booking->id);
                $bookingId = $booking->id;

                $paymentHistoriesTable = $this->fetchTable('PaymentHistories');
                $placeholderPayment = $paymentHistoriesTable->newEntity([
                    'booking_id' => $bookingId,
                    'customer_id' => $booking->customer_id,
                    'payment_amount' => $booking->total_cost,
                    'payment_currency' => 'AUD',
                    'payment_status' => 'Pending',
                    'payment_method' => null,
                    'payment_date' => FrozenTime::now(),
                    'notes' => 'Placeholder record created on booking confirmation.'
                ]);
                if (!$paymentHistoriesTable->save($placeholderPayment)) {
                    Log::error('Failed to save placeholder PaymentHistory for Booking ID: ' . $bookingId . ' Errors: ' . json_encode($placeholderPayment->getErrors()));
                    $this->Flash->warning('Booking confirmed, but there was an issue initializing payment record. Please contact support if payment issues occur.');
                }

                try {
                    $bookingWithDetails = $this->Bookings->get($bookingId, [
                        'contain' => ['Customers', 'BookingsServices.Services']
                    ]);
                    // Ensure placeholderPayment is the entity saved above
                    if ($bookingWithDetails && $placeholderPayment) {
                        $mailer = new \App\Mailer\InvoiceMailer();
                        $mailer->sendBookingConfirmedInvoice($bookingWithDetails, $placeholderPayment);
                        Log::info("Booking confirmation email sent for Booking ID: {$bookingId} to {$bookingWithDetails->customer->email}");
                    } else {
                        Log::error("Could not fetch booking/payment details needed to send confirmation email for Booking ID: {$bookingId}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send booking confirmation email for Booking ID: {$bookingId}. Error: " . $e->getMessage());
                }

                $allServicesTimes = [];

                // Fetch service durations
                Log::debug('[CustomerBooking] Fetching service durations...');
                $serviceIds = array_column($data['bookings_services'] ?? [], 'service_id');
                $servicesDetails = [];
                if (!empty($serviceIds)) {
                    $servicesDetails = $this->Services->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes',
                    ])->where(['id IN' => $serviceIds])->toArray();
                    Log::debug('[CustomerBooking] Durations fetched: ' . json_encode($servicesDetails));
                } else {
                    Log::debug('[CustomerBooking] No service IDs found for duration fetch.');
                }

                // Save BookingsServices records with individual start/end times
                if (!empty($data['bookings_services'])) {
                     Log::debug('[CustomerBooking] Starting loop to save BookingsServices...');
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                         Log::debug('[CustomerBooking] Processing service data: ' . json_encode($serviceData));
                        // Ensure all needed keys exist
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            Log::warning('Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }

                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;

                        if ($duration <= 0) {
                            Log::warning("Skipping service ID {$serviceId} with zero or invalid duration.");
                            continue;
                        }

                        try {
                            $startTime = new DateTime($data['booking_date'] . ' ' . $startTimeString);
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");

                            $bookingService = $bookingsServicesTable->newEntity([
                                'booking_id' => $bookingId,
                                'service_id' => $serviceId,
                                'stylist_id' => (int)$serviceData['stylist_id'],
                                'start_time' => $startTime->format('H:i:s'),
                                'end_time' => $endTime->format('H:i:s'),
                                'service_cost' => $serviceData['service_cost'],
                            ]);

                            if ($bookingsServicesTable->save($bookingService)) {
                                 Log::debug('[CustomerBooking] Saved BookingsService for Service ID: ' . $serviceId);
                                $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                                Log::debug('Successfully saved booking service with times');
                            } else {
                                Log::error('Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                                // Decide if we should rollback or just flag the error
                            }
                        } catch (Exception $e) {
                            Log::error("Error processing time for service {$serviceId}: " . $e->getMessage());
                        }
                    }
                    Log::debug('[CustomerBooking] Finished loop saving BookingsServices.');
                } else {
                     Log::debug('[CustomerBooking] No bookings_services data found to save.');
                }

                // Create BookingsStylists records (without overall times)
                if (!empty($data['bookings_services'])) {
                    Log::debug('[CustomerBooking] Starting loop to save BookingsStylists...');
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
                                Log::error('Failed to save booking stylist record for stylist ' . $stylistId . ' Errors: ' . json_encode($bookingStylist->getErrors()));
                                $this->Flash->error(__('Your booking was saved, but some stylist details could not be saved.'));
                            }
                            $processedStylists[] = $stylistId;
                        }
                    }
                     Log::debug('[CustomerBooking] Finished loop saving BookingsStylists.');
                } else {
                    Log::debug('[CustomerBooking] No bookings_services data found for saving BookingsStylists.');
                }

                // Calculate and Update Overall Booking Times
                $overallStartTime = null;
                $overallEndTime = null;
                if (!empty($allServicesTimes)) {
                    $startTimestamps = array_map(function($t) { return $t['start']->getTimestamp(); }, $allServicesTimes);
                    $endTimestamps = array_map(function($t) { return $t['end']->getTimestamp();}, $allServicesTimes);

                    if (!empty($startTimestamps)) {
                        $minStartTs = min($startTimestamps);
                        $overallStartTime = (new DateTime())->setTimestamp($minStartTs);
                    }
                    if (!empty($endTimestamps)) {
                        $maxEndTs = max($endTimestamps);
                        $overallEndTime = (new DateTime())->setTimestamp($maxEndTs);
                    }
                }

                // Patch and save the overall times to the main booking record
                $booking = $this->Bookings->patchEntity($booking, [
                    'start_time' => $overallStartTime ? $overallStartTime->format('H:i:s') : null,
                    'end_time' => $overallEndTime ? $overallEndTime->format('H:i:s') : null
                ]);
                if (!$this->Bookings->save($booking, ['checkRules' => false])) {
                     $this->Flash->warning('Booking saved, but failed to update overall times.');
                } else {
                     Log::debug('[CustomerBooking] Successfully saved overall times.');
                }
                $this->Flash->success(__('Your booking is confirmed! Please see payment options below. You will receive an invoice via email shortly.'));
                Log::debug('[CustomerBooking] Redirecting to customerview for payment. ID: ' . $bookingId);
                return $this->redirect(['action' => 'customerview', $bookingId]); // Redirect to customerview
            }

            Log::error('Failed to save initial booking. Errors: ' . json_encode($booking->getErrors()));
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'stylists', 'services'));
    }

    public function adminbooking()
    {
        $booking = $this->Bookings->newEmptyEntity();
        if ($this->request->is('post')) {
            $data = $this->request->getData();

            if (isset($data['booking_date'])) {
                $data['booking_date_formatted'] = $data['booking_date'];
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
            $data['remaining_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? null;

            // Create a temporary booking entity without associations first
            $booking = $this->Bookings->newEntity([
                'customer_id' => $data['customer_id'],
                'booking_name' => $data['booking_name'],
                'booking_date' => $data['booking_date_formatted'],
                'total_cost' => $data['total_cost'],
                'remaining_cost' => $data['remaining_cost'],
                'notes' => $data['notes'] ?? null,
                'status' => 'Confirmed - Payment Due',
            ]);

            // Server-Side Time Conflict Validation
            $stylistTimeSlotsAdmin = [];
            $hasConflictAdmin = false;
            $conflictMessageAdmin = '';

            if (!empty($data['bookings_services'])) {
                $serviceIdsForValidationAdmin = array_column($data['bookings_services'], 'service_id');
                $servicesDetailsForValidationAdmin = [];
                if (!empty($serviceIdsForValidationAdmin)) {
                     $servicesDetailsForValidationAdmin = $this->fetchTable('Services')->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes',
                     ])->where(['id IN' => $serviceIdsForValidationAdmin])->toArray();
                }

                foreach ($data['bookings_services'] as $serviceData) {
                    if (!isset($serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_id'])) {
                         continue;
                    }
                    $stylistIdAdmin = (int)$serviceData['stylist_id'];
                    $serviceIdAdmin = (int)$serviceData['service_id'];
                    $startTimeStrAdmin = $serviceData['start_time'];
                    // Use the already formatted date from earlier in the action
                    $bookingDateStrAdmin = $data['booking_date_formatted'];
                    $durationAdmin = $servicesDetailsForValidationAdmin[$serviceIdAdmin] ?? 0;

                    if ($durationAdmin <= 0) continue;

                    try {
                         $startTimeAdmin = new DateTime($bookingDateStrAdmin . ' ' . $startTimeStrAdmin);
                         $endTimeAdmin = clone $startTimeAdmin;
                         $endTimeAdmin->modify("+{$durationAdmin} minutes");
                         $newSlotAdmin = ['start' => $startTimeAdmin->getTimestamp(), 'end' => $endTimeAdmin->getTimestamp(), 'service_id' => $serviceIdAdmin];

                        if (isset($stylistTimeSlotsAdmin[$stylistIdAdmin])) {
                            foreach ($stylistTimeSlotsAdmin[$stylistIdAdmin] as $existingSlotAdmin) {
                                if ($newSlotAdmin['start'] < $existingSlotAdmin['end'] && $newSlotAdmin['end'] > $existingSlotAdmin['start']) {
                                    $hasConflictAdmin = true;
                                    $conflictMessageAdmin = "Time conflict detected for one of the selected stylists. Please ensure service times do not overlap.";
                                    break 2;
                                }
                            }
                        }
                         $stylistTimeSlotsAdmin[$stylistIdAdmin][] = $newSlotAdmin;

                    } catch (Exception $e) {
                         Log::error("[Admin Validation] Time processing error: " . $e->getMessage());
                         $this->Flash->error(__('An error occurred while validating booking times. Please check the selected times.'));
                         // Reload necessary data for the admin view
                         $customers = $this->Bookings->Customers->find('list', ['limit' => 200])->all();
                         $services = $this->fetchTable('Services')->find('all')->all();
                         $this->set(compact('booking', 'customers', 'services'));

                         return $this->render('adminbooking');
                    }
                }
            }

            if ($hasConflictAdmin) {
                $this->Flash->error($conflictMessageAdmin ?: __('A time conflict was detected. Please ensure service times for the same stylist do not overlap.'));
                // Reload necessary data for the admin view
                $customers = $this->Bookings->Customers->find('list', ['limit' => 200])->all();
                $services = $this->fetchTable('Services')->find('all')->all();
                $booking = $this->Bookings->patchEntity($booking, $data, ['associated' => []]);
                $this->set(compact('booking', 'customers', 'services'));
                $this->request = $this->request->withParsedBody($data);

                return $this->render('adminbooking');
            }

            // Try saving the main booking record
            if ($this->Bookings->save($booking)) {
                $bookingId = $booking->id;

                $paymentHistoriesTable = $this->fetchTable('PaymentHistories');
                $placeholderPayment = $paymentHistoriesTable->newEntity([
                    'booking_id' => $bookingId,
                    'customer_id' => $booking->customer_id,
                    'payment_amount' => $booking->total_cost,
                    'payment_currency' => 'AUD',
                    'payment_status' => 'Pending',
                    'payment_method' => null,
                    'payment_date' => FrozenTime::now(),
                    'notes' => 'Placeholder record created on booking by admin.'
                ]);
                if (!$paymentHistoriesTable->save($placeholderPayment)) {
                    Log::error('Failed to save placeholder PaymentHistory for Booking ID (Admin): ' . $bookingId . ' Errors: ' . json_encode($placeholderPayment->getErrors()));
                    $this->Flash->warning('Booking created, but there was an issue initializing the payment record. The customer might face issues if they need to pay online.');
                }

                try {
                    $bookingWithDetails = $this->Bookings->get($bookingId, [
                        'contain' => ['Customers', 'BookingsServices.Services']
                    ]);
                    if ($bookingWithDetails && $placeholderPayment) {
                        $mailer = new \App\Mailer\InvoiceMailer();
                        $mailer->sendBookingConfirmedInvoice($bookingWithDetails, $placeholderPayment);
                        Log::info("Booking confirmation email sent for Booking ID (Admin): {$bookingId} to {$bookingWithDetails->customer->email}");
                    } else {
                        Log::error("Could not fetch booking/payment details needed to send confirmation email for Booking ID (Admin): {$bookingId}");
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send booking confirmation email for Booking ID (Admin): {$bookingId}. Error: " . $e->getMessage());
                }

                $allServicesTimes = [];

                // Fetch service durations
                $serviceIds = array_column($data['bookings_services'] ?? [], 'service_id');
                $servicesDetails = [];
                if (!empty($serviceIds)) {
                    $servicesDetails = $this->Services->find('list', [
                        'keyField' => 'id',
                        'valueField' => 'duration_minutes',
                    ])->where(['id IN' => $serviceIds])->toArray();
                }

                // Save BookingsServices records with individual start/end times
                if (!empty($data['bookings_services'])) {
                    $bookingsServicesTable = $this->fetchTable('BookingsServices');
                    foreach ($data['bookings_services'] as $serviceIdKey => $serviceData) {
                        // Ensure all needed keys exist
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                            Log::warning('[Admin] Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }

                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;

                        if ($duration <= 0) {
                            Log::warning("[Admin] Skipping service ID {$serviceId} with zero or invalid duration.");
                            continue;
                        }

                        try {
                            // Use the already formatted date
                            $startTime = new DateTime($data['booking_date_formatted'] . ' ' . $startTimeString);
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");

                            $bookingService = $bookingsServicesTable->newEntity([
                                'booking_id' => $bookingId,
                                'service_id' => $serviceId,
                                'stylist_id' => (int)$serviceData['stylist_id'],
                                'start_time' => $startTime->format('H:i:s'),
                                'end_time' => $endTime->format('H:i:s'),
                                'service_cost' => $serviceData['service_cost'],
                            ]);

                            if ($bookingsServicesTable->save($bookingService)) {
                                $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                                Log::debug('[Admin] Successfully saved booking service with times');
                            } else {
                                Log::error('[Admin] Failed to save booking service. Errors: '
                                    . json_encode($bookingService->getErrors()));
                                // Consider adding a flash message here too
                            }
                        } catch (Exception $e) {
                            Log::error("[Admin] Error processing time for service {$serviceId}: "
                                . $e->getMessage());
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
                                'selected_cost' => $booking->total_cost,
                             ]);
                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                Log::error('[Admin] Failed to save booking stylist record for stylist '
                                    . $stylistId
                                    . ' Errors: '
                                    . json_encode($bookingStylist->getErrors()));
                                // Use a different flash message for admin
                                $this->Flash->error(__('The booking was created, but some stylist assignment details could not be saved.'));
                            }
                            $processedStylists[] = $stylistId;
                        }
                    }
                }

                // Use a different success message for admin
                $this->Flash->success(__(
                    'Booking for {0} has been saved successfully.',
                    $customer->first_name
                    . ' '
                    . $customer->last_name
                ));

                return $this->redirect(['action' => 'index']);
            }
            // Log the errors if the initial save fails
            Log::error('[Admin] Failed to save initial booking. Errors: ' . json_encode($booking->getErrors()));
            $this->Flash->error(__('The booking could not be saved. Please, try again.'));
        }

        // Pass variables needed for the form
        $customers = $this->Bookings->Customers->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set(compact('booking', 'customers', 'services'));
    }

    /**
     * @return Response|void|null
     * @throws \Exception
     */
    public function guestbooking()
    {
        $customersTable = $this->fetchTable('Customers');
        $guest = $customersTable->find()
            ->where(['type' => 'guest'])
            ->first();

        if (empty($guest)) {
            $this->Flash->error(__('Sorry Guest Booking is not available at the moment. Please try again later'));
            return $this->redirect(['controller' => 'Pages', 'action' => 'display']);
        }

        if (!$this->Authentication->getIdentity()) {
            $guestUser = $customersTable->find()
                ->where([
                    'type' => 'guest',
                    'email' => 'guest@chiccharm.com',
                ])
                ->first();
            if ($guestUser) {
                $this->Authentication->setIdentity($guestUser);
            }
        }

        $bookingEntity = $this->Bookings->newEmptyEntity();

        if ($this->request->is('post')) {
            if ($this->Recaptcha->verify()) {
                $data = $this->request->getData();

                // Automatically set customer_id from the fetched guest account
                $data['customer_id'] = $guest->id;
                // Ensure booking_name is set (uses customer_name from form)
                if (empty($data['customer_name'])) {
                    $this->Flash->error(__('Please enter your name for the booking.'));
                    // Set $bookingEntity with current data for form repopulation
                    $this->set('booking', $this->Bookings->patchEntity($bookingEntity, $data));
                    $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
                    $services = $this->fetchTable('Services')->find('all')->all();
                    $this->set(compact('stylists', 'services'));
                    return $this->render('guestbooking');
                }
                $data['booking_name'] = 'Booking for ' . h($data['customer_name']);

                // Calculate total cost from all selected services
                $totalCost = 0;
                if (!empty($data['bookings_services'])) {
                    foreach ($data['bookings_services'] as $serviceData) {
                        $totalCost += floatval($serviceData['service_cost'] ?? 0);
                    }
                }
                $data['total_cost'] = $totalCost;
                $data['remaining_cost'] = $totalCost;
                $data['notes'] = $data['notes'] ?? null;

                $stylistTimeSlots = [];
                $hasConflict = false;
                $conflictMessage = '';

                if (!empty($data['bookings_services'])) {
                    $serviceIdsForValidation = array_column($data['bookings_services'], 'service_id');
                    $servicesDetailsForValidation = [];
                    if (!empty($serviceIdsForValidation)) {
                         $servicesDetailsForValidation = $this->fetchTable('Services')->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'duration_minutes',
                         ])->where(['id IN' => $serviceIdsForValidation])->toArray();
                    }

                    foreach ($data['bookings_services'] as $serviceData) {
                        if (!isset($serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_id'])) {
                             continue;
                        }
                        $stylistId = (int)$serviceData['stylist_id'];
                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeStr = $serviceData['start_time'];
                        $duration = $servicesDetailsForValidation[$serviceId] ?? 0;

                        if ($duration <= 0) continue;

                        try {
                             $startTime = new DateTime($data['booking_date'] . ' ' . $startTimeStr);
                             $endTime = clone $startTime;
                             $endTime->modify("+{$duration} minutes");
                             $newSlot = ['start' => $startTime->getTimestamp(), 'end' => $endTime->getTimestamp(), 'service_id' => $serviceId];

                            if (isset($stylistTimeSlots[$stylistId])) {
                                foreach ($stylistTimeSlots[$stylistId] as $existingSlot) {
                                    if ($newSlot['start'] < $existingSlot['end'] && $newSlot['end'] > $existingSlot['start']) {
                                        $hasConflict = true;
                                        $conflictMessage = "Time conflict detected for one of the selected stylists. Please ensure service times do not overlap.";
                                        break 2;
                                    }
                                }
                            }
                             $stylistTimeSlots[$stylistId][] = $newSlot;
                        } catch (Exception $e) {
                             Log::error('[GuestBooking] Validation time processing error: ' . $e->getMessage());
                             $this->Flash->error(__('An error occurred while validating booking times. Please check the selected times.'));
                             $this->set('booking', $this->Bookings->patchEntity($bookingEntity, $data));
                             $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
                             $services = $this->fetchTable('Services')->find('all')->all();
                             $this->set(compact('stylists', 'services'));
                             return $this->render('guestbooking');
                        }
                    }
                }

                if ($hasConflict) {
                    $this->Flash->error($conflictMessage ?: __('A time conflict was detected. Please ensure service times for the same stylist do not overlap.'));
                    $this->set('booking', $this->Bookings->patchEntity($bookingEntity, $data));
                    $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
                    $services = $this->fetchTable('Services')->find('all')->all();
                    $this->request = $this->request->withParsedBody($data);
                    return $this->render('guestbooking');
                }

                $allServicesTimesForSession = [];
                if (!empty($data['bookings_services'])) {
                    foreach ($data['bookings_services'] as $key => $serviceData) {
                        if (!isset($serviceData['service_id'], $serviceData['start_time'])) continue;
                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetailsForValidation[$serviceId] ?? 0;
                        if ($duration <= 0) continue;

                        try {
                            $startTime = new DateTime($data['booking_date'] . ' ' . $startTimeString);
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");
                            $data['bookings_services'][$key]['start_time_formatted'] = $startTime->format('H:i:s');
                            $data['bookings_services'][$key]['end_time_formatted'] = $endTime->format('H:i:s');
                            $allServicesTimesForSession[] = ['start' => $startTime, 'end' => $endTime];
                        } catch (Exception $e) {
                            // Should not happen if validation passed, but good to log
                            Log::error('[GuestBooking] Error calculating final service times for session: ' . $e->getMessage());
                        }
                    }
                }

                // Calculate overall start/end times for the booking to store in session
                $overallStartTimeForSession = null;
                $overallEndTimeForSession = null;
                if (!empty($allServicesTimesForSession)) {
                    $startTimestamps = array_map(function($t) { return $t['start']->getTimestamp(); }, $allServicesTimesForSession);
                    $endTimestamps = array_map(function($t) { return $t['end']->getTimestamp(); }, $allServicesTimesForSession);
                    if (!empty($startTimestamps)) {
                        $overallStartTimeForSession = (new DateTime())->setTimestamp(min($startTimestamps))->format('H:i:s');
                    }
                    if (!empty($endTimestamps)) {
                        $overallEndTimeForSession = (new DateTime())->setTimestamp(max($endTimestamps))->format('H:i:s');
                    }
                }
                $data['overall_start_time'] = $overallStartTimeForSession;
                $data['overall_end_time'] = $overallEndTimeForSession;

                // Show service and stylist names in the booking summary
                $enrichedBookingServices = [];
                if (!empty($data['bookings_services'])) {
                    $serviceIds = array_unique(array_column($data['bookings_services'], 'service_id'));
                    $stylistIds = array_unique(array_column($data['bookings_services'], 'stylist_id'));

                    $serviceNameMap = [];
                    if (!empty($serviceIds)) {
                        $serviceNameMap = $this->Services->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'service_name'
                        ])->where(['id IN' => $serviceIds])->toArray();
                    }

                    $stylistNameMap = [];
                    if (!empty($stylistIds)) {
                        $stylistNameMap = $this->Stylists->find('list', [
                            'keyField' => 'id',
                            'valueField' => function($stylist) { return $stylist->first_name . ' ' . $stylist->last_name; }
                        ])->where(['id IN' => $stylistIds])->toArray();
                    }

                    foreach ($data['bookings_services'] as $bs) {
                        $enrichedBs = $bs;
                        $enrichedBs['service_name'] = $serviceNameMap[$bs['service_id']] ?? 'Unknown Service';
                        $enrichedBs['stylist_name'] = $stylistNameMap[$bs['stylist_id']] ?? 'Unknown Stylist';
                        $enrichedBookingServices[] = $enrichedBs;
                    }
                }
                $data['bookings_services_summary'] = $enrichedBookingServices;

                // Generate and add a unique token for the pending booking
                $pendingBookingToken = bin2hex(random_bytes(16));
                $data['pending_booking_token'] = $pendingBookingToken;

                Log::debug('[GuestBooking] Data being written to session: ' . json_encode($data), ['scope' => ['guest_session']]);

                $this->request->getSession()->write('GuestBooking.pending_details', $data);

                // Redirect to the view pending booking page with the token
                $this->Flash->success(__('Your booking details are summarized below. Please complete your payment to confirm.'));
                return $this->redirect(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $pendingBookingToken]);

            } else { // Recaptcha failed
                $this->Flash->error(__('Please confirm that you are not a bot.'));
                $this->set('booking', $this->Bookings->patchEntity($bookingEntity, $this->request->getData()));
            }
        }

        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();
        $this->set('booking', $bookingEntity);
        $this->set(compact('stylists', 'services'));
        $this->render('guestbooking');
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
        $booking = $this->Bookings->get($id,
            contain: [
                'Customers',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name']
                    ]
                ],
                'BookingsServices' => [
                    'Services',
                    'Stylists'
                    ],
                'PaymentHistories' => [
                    'fields' => [
                        'id',
                        'booking_id',
                        'invoice_pdf',
                        'payment_date',
                        'payment_method',
                        'payment_status'
                    ],
                    'sort' => ['PaymentHistories.payment_date' => 'DESC']
                ]
            ]
        );

        if (!empty($booking->payment_histories)) {
            $booking->latest_payment_history = $booking->payment_histories[0];
        } else {
            $booking->latest_payment_history = null;
        }

        $this->set(compact('booking'));
    }

    /**
     * Stylist View Method
     *
     * @param string|null $id Booking id
     * @return void
     */
    public function stylistview($id = null)
    {
        $booking = $this->Bookings->get(
            $id,
            contain: [
                'Customers',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost'],
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
            ],
        );

        $this->set(compact('booking'));
    }

    public function stylistPastBookings () {
        $stylist = $this->Stylists->get($this->Authentication->getIdentity()->id);
        $today = FrozenDate::today();

        //Bookings that have the selected Stylist
        $bookingsTable = $this->fetchTable('Bookings');
        $query = $bookingsTable->find()
            ->contain([
                'BookingsStylists',
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
            ->where([
                'Bookings.status' => 'Confirmed - Paid',
                'Bookings.booking_date <' => $today
            ])
            ->orderBy(['Bookings.booking_date' => 'DESC']);
        $bookings = $this->paginate($query);

        $this->set(compact('bookings'));
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
        $endTimeObj = new DateTime($endTime);
        $closingTime = new DateTime('17:00');

        if ($endTimeObj > $closingTime) {
            return $this->response->withStringBody(json_encode([
                'message' => 'Note: The shop closes at 5 PM. Please select an earlier time slot.',
                'stylists' => [],
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
                    'error' => 'One or more services not found: ' . implode(', ', $invalidServiceIds),
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
                            'BookingsStylists.end_time >' => $startTime,
                        ],
                        [
                            'BookingsStylists.start_time <' => $endTime,
                            'BookingsStylists.end_time >' => $endTime,
                        ],
                        [
                            'BookingsStylists.start_time >=' => $startTime,
                            'BookingsStylists.end_time <=' => $endTime,
                        ],
                    ],
                    'BookingsStylists.start_time IS NOT NULL',
                    'BookingsStylists.end_time IS NOT NULL',
                ]);
            });

            $stylists = $stylistsQuery->toArray();

            $this->log('Available stylists for services ' . implode(',', $serviceIds) . ': ' . count($stylists));

            // Format the results
            $formattedStylists = array_map(function ($stylist) {
                return [
                    'id' => $stylist->id,
                    'name' => $stylist->first_name . ' ' . $stylist->last_name,
                ];
            }, $stylists);

            return $this->response->withStringBody(json_encode($formattedStylists));
        } catch (Exception $e) {
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
                'status' => 'active',
            ])
            ->contain([
                'Customers',
                'Services',
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
            ])
            ->orderBy(['booking_date' => 'ASC'])
            ->limit(5);// Only show the next 5 upcoming bookings

        $bookings = $query->all();
        $this->set(compact('bookings'));
    }

    /**
     * Private helper to calculate available start time slots for a single service/stylist/date.
     *
     * @param string $date (Y-m-d)
     * @param int $serviceId
     * @param int $stylistId
     * @param int|null $bookingIdToExclude Optional: ID of the booking being edited.
     * @return array List of available start time strings (H:i format), or empty array on error/no slots.
     */
    private function _calculateAvailableSlots(string $date, int $serviceId, int $stylistId, ?int $bookingIdToExclude = null): array
    {
        // Log entry and parameters
        $this->log("_calculateAvailableSlots: Date={$date}, Svc={$serviceId}, Stylist={$stylistId}, ExcludeBooking=" . ($bookingIdToExclude !== null ? $bookingIdToExclude : 'NULL'), 'debug');
        try {
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
            $closingTime = new DateTime($date . ' 17:00:00');

            for ($hour = $startHour; $hour < $endHour; $hour++) {
                for ($minute = 0; $minute < 60; $minute += $interval) {
                    // Calculate potential start/end times
                    $slotStartString = sprintf('%02d:%02d:00', $hour, $minute);
                    $potentialStartTime = new DateTime($date . ' ' . $slotStartString);
                    $potentialEndTime = clone $potentialStartTime;
                    $potentialEndTime->modify("+{$duration} minutes");

                    // Log the attempt for this specific minute slot BEFORE checking anything else
                    $this->log("  Trying Slot: {$slotStartString}", 'debug');

                    // Check if the service finishes by closing time
                    if ($potentialEndTime > $closingTime) {
                        $this->log("Slot {$slotStartString} for Svc {$serviceId} skipped: ends after closing time.", 'debug');
                        break;
                    }

                    // Check availability using the existing helper, PASSING the bookingIdToExclude
                    $isSegmentAvailable = $this->checkSegmentAvailability(
                        $date,
                        $potentialStartTime->format('H:i:s'),
                        $potentialEndTime->format('H:i:s'),
                        $serviceId,
                        $stylistId,
                        $bookingIdToExclude // Pass the ID here
                    );

                    // Log the check result for each potential slot
                    $this->log("  Slot Check: Time=" . $potentialStartTime->format('H:i:s') . "-" . $potentialEndTime->format('H:i:s') . " Available=" . ($isSegmentAvailable ? 'Yes' : 'No'), 'debug');

                    if ($isSegmentAvailable) {
                        // Check if the slot is in the past (only for today)
                        $todayStr = (new DateTime())->format('Y-m-d');
                        $now = new DateTime();
                        // Log the values being compared for the past check
                        $this->log("  Past Check: Date={$date}, Today={$todayStr}, SlotStart=" . $potentialStartTime->format('Y-m-d H:i:s') . ", Now=" . $now->format('Y-m-d H:i:s'), 'debug');
                        if ($date === $todayStr && $potentialStartTime < $now) {
                            $this->log("  Slot Check: {$slotStartString} Skipped: In the past.", 'debug');
                            continue;
                        }

                        $this->log("  >>> Reached point to ADD slot: {$slotStartString}", 'debug');
                        $this->log("  Slot Check: {$slotStartString} ADDED.", 'debug');
                        // +++ Log before format +++
                        $this->log("    Attempting format on: " . var_export($potentialStartTime, true), 'debug');
                        $timeToAdd = $potentialStartTime->format('H:i');
                        // +++ Log after format +++
                        $this->log("    Formatted time: {$timeToAdd}", 'debug');
                        $availableSlots[] = $timeToAdd;
                        // +++ Log after append +++
                        $this->log("    Appended. Current array: " . json_encode($availableSlots), 'debug');
                    }
                }
            }

            // Log the final array before returning
            $this->log("_calculateAvailableSlots: Returning slots: " . json_encode($availableSlots), 'debug');

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
     * Expects input format: { date: 'YYYY-MM-DD', selected_services: [{service_id: X, stylist_id: Y|'any'}, ...], booking_id: Z (optional) }
     *
     * @return \Cake\Http\Response|null|void Returns JSON response with available start times
     */
    public function getAvailableTimeSlots()
    {
        $this->request->allowMethod(['post']);
        $this->autoRender = false;
        $this->response = $this->response->withType('application/json');

        $data = $this->request->getData();
        $date = $data['date'] ?? null; // Date should now be Y-m-d directly
        $selectedServiceInput = $data['selected_services'][0] ?? null;
        $bookingIdToExclude = isset($data['booking_id']) ? (int)$data['booking_id'] : null;

        // Log received data
        $this->log("getAvailableTimeSlots: Received Data: " . json_encode($data), 'debug');
        $this->log("getAvailableTimeSlots: Extracted ExcludeBooking=" . ($bookingIdToExclude !== null ? $bookingIdToExclude : 'NULL'), 'debug');

        if (!$date || !$selectedServiceInput || !isset($selectedServiceInput['service_id']) || !isset($selectedServiceInput['stylist_id'])) {
            $this->log('getAvailableTimeSlots: Invalid input data received: ' . json_encode($data), 'warning');
            return $this->response->withStatus(400)->withStringBody(json_encode(['error' => 'Missing or invalid date, service_id, or stylist_id.']));
        }

        // Removed parsing from dd/MM/yyyy as date should be Y-m-d
        // $this->log("getAvailableTimeSlots: Date from input '{$date}' (expected Y-m-d)", 'debug');

        try {
            $serviceId = (int)$selectedServiceInput['service_id'];
            $stylistId = (int)$selectedServiceInput['stylist_id'];

            // Call the refactored helper method, passing the bookingIdToExclude
            $availableSlotsHi = $this->_calculateAvailableSlots($date, $serviceId, $stylistId, $bookingIdToExclude);

            $this->log("getAvailableTimeSlots: Received raw slots from helper: " . json_encode($availableSlotsHi), 'debug');

            // If no slots were found by the helper, return empty array immediately
            if (empty($availableSlotsHi)) {
                $this->log("getAvailableTimeSlots: No slots returned from helper, returning empty JSON.", 'debug');
                return $this->response->withStringBody(json_encode([]));
            }

            // Format the H:i slots into the required value/text format
            $formattedSlots = [];
            foreach ($availableSlotsHi as $slotHi) {
                $this->log("  Formatting loop: Processing slot '{$slotHi}'", 'debug');
                try {
                    $timeObj = FrozenTime::createFromFormat('H:i', $slotHi);
                    if ($timeObj) {
                        $this->log("    -> Parsed successfully.", 'debug');
                        $formattedSlots[] = [
                            'value' => $slotHi,
                            'text' => $timeObj->format('h:i A')
                        ];
                    } else {
                        $this->log("    -> FAILED to parse '{$slotHi}' with FrozenTime.", 'warning');
                        $this->log("Failed to parse time slot '{$slotHi}' in getAvailableTimeSlots.", 'warning');
                    }
                } catch (Exception $e) {
                     $this->log("    -> EXCEPTION parsing '{$slotHi}': " . $e->getMessage(), 'error');
                     $this->log("Exception parsing time slot '{$slotHi}' in getAvailableTimeSlots: " . $e->getMessage(), 'error');
                }
            }
            $this->log("getAvailableTimeSlots: Returning formatted slots: " . json_encode($formattedSlots), 'debug');
            return $this->response->withStringBody(json_encode($formattedSlots));

        } catch (Throwable $e) {
            $this->log('Error in getAvailableTimeSlots (single): ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString(), 'error');
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
    private function checkSegmentAvailability(string $date, string $startTimeString, string $endTimeString, int $serviceId, int $stylistId, ?int $bookingIdToExclude = null): bool
    {
        try {
            // Log the exclusion ID if provided
            $logSuffix = $bookingIdToExclude ? " (Excluding Booking ID: {$bookingIdToExclude})" : '';
            $this->log("checkSegmentAvailability: Checking Svc={$serviceId}, Stylist={$stylistId}, Date={$date}, Time={$startTimeString}-{$endTimeString}{$logSuffix}", 'debug');

            // 1. Check qualification
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

            $query = $bookingsServicesTable->find()
                ->innerJoinWith('Bookings', function ($q) use ($date) {
                    // Also ensure the booking itself isn't cancelled
                    return $q->where([
                        'Bookings.booking_date' => $date,
                        'Bookings.status !=' => 'cancelled',
                    ]);
                })
                ->where([
                    'BookingsServices.stylist_id' => $stylistId,
                    'BookingsServices.start_time <' => $endTimeString,
                    'BookingsServices.end_time >' => $startTimeString,
                    'BookingsServices.start_time IS NOT NULL',
                    'BookingsServices.end_time IS NOT NULL',
                ]);

            // Add condition to exclude the current booking if ID is provided
            if ($bookingIdToExclude !== null) {
                $query->where(['BookingsServices.booking_id !=' => $bookingIdToExclude]);
                $this->log("checkSegmentAvailability: Excluding booking ID {$bookingIdToExclude} from conflict check.", 'debug');
            }

            $conflictCount = $query->count();

            $hasConflict = $conflictCount > 0;
            $this->log("checkSegmentAvailability: Conflict check result: " . ($hasConflict ? 'Conflict Found ({$conflictCount})' : 'No Conflict'), 'debug');

            return !$hasConflict;
        } catch (Throwable $e) {
            $this->log('Error in checkSegmentAvailability: '
                . $e->getMessage()
                . ' Trace: '
                . $e->getTraceAsString(), 'error');

            return false;
        }
    }

    /**
     * Update booking statuses to finished for past bookings
     *
     * @return void
     */
    public function updateBookingStatuses()
    {
        $now = new CakeDateTime();

        // Find all active bookings that have ended
        $pastBookings = $this->Bookings->find()
            ->where([
                'status' => 'active',
                'booking_date <=' => $now->format('Y-m-d'),
                'end_time <' => $now->format('H:i:s'),
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
                'Bookings.notes',
            ])
            ->where(['status IN' => ['finished', 'cancelled']])
            ->contain([
                'Customers' => [
                    'fields' => ['id', 'first_name', 'last_name'],
                ],
                'BookingsServices' => [
                    'Services' => [
                        'fields' => ['id', 'service_name', 'service_cost'],
                    ],
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
            ]);

        // Search functionality
        $search = $this->request->getQuery('search');
        if ($search) {
            $query->where([
                'OR' => [
                    'Bookings.booking_name LIKE' => '%' . $search . '%',
                    'Customers.first_name LIKE' => '%' . $search . '%',
                    'Customers.last_name LIKE' => '%' . $search . '%',
                ]
            ]);
        }

        // Filter by date range
        $dateRange = $this->request->getQuery('date_range');
        if ($dateRange) {
            $now = new CakeDateTime();
            switch ($dateRange) {
                case 'last_week':
                    $startDate = $now->modify('-1 week');
                    break;
                case 'last_month':
                    $startDate = $now->modify('-1 month');
                    break;
                case 'last_3_months':
                    $startDate = $now->modify('-3 months');
                    break;
                case 'last_6_months':
                    $startDate = $now->modify('-6 months');
                    break;
                case 'last_year':
                    $startDate = $now->modify('-1 year');
                    break;
                default:
                    $startDate = null;
            }
            if ($startDate) {
                $query->where(['Bookings.booking_date >=' => $startDate->format('Y-m-d')]);
            }
        }

        $query->orderBy(['booking_date' => 'DESC']);
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

        $today = FrozenDate::today();
        $query = $this->Bookings->find()
            ->where([
                'customer_id' => $user->id,
                'OR' => [
                    ['Bookings.status' => 'cancelled'], 
                    [
                        'Bookings.status IN' => ['finished', 'Confirmed - Payment Due', 'Confirmed - Paid'],
                        'Bookings.booking_date <' => $today,
                    ]
                ]
            ])
            ->contain([
                'BookingsServices' => [
                    'Services',
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
                'BookingsStylists' => [
                    'Stylists' => [
                        'fields' => ['id', 'first_name', 'last_name'],
                    ],
                ],
            ])
            ->orderBy(['booking_date' => 'DESC']);
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
                    'Stylists.last_name',
                ])
                ->distinct(['Stylists.id'])
                ->innerJoinWith('Services', function ($q) use ($serviceId) {
                    return $q->where(['Services.id' => $serviceId]);
                })
                ->order([
                    'Stylists.first_name' => 'ASC',
                    'Stylists.last_name' => 'ASC',
                ])
                ->toArray();

            // Format the results for easy use in a dropdown/select list
            $formattedStylists = array_map(function ($stylist) {
                return [
                    'id' => $stylist->id,
                    'name' => $stylist->first_name . ' ' . $stylist->last_name,
                ];
            }, $stylists);

            return $this->response->withStringBody(json_encode($formattedStylists));

        } catch (Throwable $e) {
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

                $availableSlots = $this->_calculateAvailableSlots($date, $serviceId, $stylistId);
                $availableSlotsCount = count($availableSlots);

                $responseData['availableSlotsCount'] = $availableSlotsCount;
            } catch (Exception $e) {
                Log::error('Error in getAvailabilityCount: '
                    . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());

                $this->response = $this->response->withStatus(500);
                $responseData['error'] = 'An internal error occurred while checking availability.';
            }
        }

        // Set the response data and serialization keys
        $this->set($responseData);
        $this->viewBuilder()->setOption('serialize', array_keys($responseData));
    }

    /**
     * Customer Edit method
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null|void Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function customeredit($id = null)
    {
        $user = $this->Authentication->getIdentity();
        // Check if user is a customer
        if (!$user || $user->type !== 'customer') {
             $this->Flash->error(__('Access denied. Please log in as a customer.'));
             return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }

        $booking = $this->Bookings->get($id, contain: [
            'Customers',
            'BookingsStylists' => ['Stylists'],
            'BookingsServices' => ['Services', 'Stylists'],
        ]);

        if ($booking->status === 'Confirmed - Paid') {
            $this->Flash->error(__('This booking has been paid and can no longer be edited.'));
            return $this->redirect(['action' => 'customerindex']);
        }

        $currentUserId = $this->Authentication->getIdentity()->id;

        // Check if the booking belongs to the logged-in customer
        if ($booking->customer_id !== $currentUserId) {
            $this->Flash->error(__('You are not authorized to edit this booking.'));
            // Redirect to customer's booking list or dashboard
            return $this->redirect(['action' => 'customerindex']);
        }

        // Check the 24-hour rule
        // Combine booking date and start time to get the full booking datetime
        $bookingDateTimeStr = $booking->booking_date->format('Y-m-d') . ' ' . ($booking->start_time ? $booking->start_time->format('H:i:s') : '00:00:00');
        try {
            $bookingDateTime = new FrozenTime($bookingDateTimeStr);
            $now = new FrozenTime();
            $minEditTime = $now->addHours(3);

            if ($bookingDateTime <= $minEditTime) {
                $this->Flash->error(__('Bookings cannot be changed less than 3 hours before the scheduled time.'));
                return $this->redirect(['action' => 'customerindex']);
            }
        } catch (Exception $e) {
            // Handle potential parsing errors if time is invalid
            Log::error('Error parsing booking date/time for edit check: ' . $e->getMessage());
            $this->Flash->error(__('Could not verify booking time for editing. Please contact support.'));
            return $this->redirect(['action' => 'customerindex']);
        }


        if ($this->request->is(['patch', 'post', 'put'])) {
            $data = $this->request->getData();
            $bookingId = $booking->id;

            // Date should be Y-m-d from native date input
            if (isset($data['booking_date'])) {
                $data['booking_date_formatted'] = $data['booking_date'];
            } else {
                $this->Flash->error(__('Booking date is missing.'));

                return $this->redirect(['action' => 'edit', $bookingId]);
            }

            // Customer ID and Name should be fixed to the logged-in user
            $data['customer_id'] = $currentUserId;
            // Regenerate booking name based on logged-in customer
            $customer = $this->Bookings->Customers->get($currentUserId);
            $data['booking_name'] = 'Booking for ' . $customer->first_name . ' ' . $customer->last_name;


            // Calculate total cost from all selected services
            $totalCost = 0;
            if (!empty($data['bookings_services'])) {
                foreach ($data['bookings_services'] as $serviceData) {
                    // Ensure cost is retrieved correctly, might need service ID lookup
                    // Assuming service_cost is submitted with the form
                    $totalCost += floatval($serviceData['service_cost'] ?? 0);
                }
            }
            $data['total_cost'] = $totalCost;
            $data['remaining_cost'] = $totalCost;
            $data['notes'] = $data['notes'] ?? $booking->notes;
            $data['refund_due_amount'] = 0.00; 


            $hasServiceData = !empty($data['bookings_services']);

            $connection = $this->Bookings->getConnection();
            try {
                $connection->begin();

                // Delete existing BookingsServices and BookingsStylists for this booking
                $bookingsServicesTable = $this->fetchTable('BookingsServices');
                $bookingsStylistsTable = $this->fetchTable('BookingsStylists');
                $bookingsServicesTable->deleteAll(['booking_id' => $bookingId]);
                $bookingsStylistsTable->deleteAll(['booking_id' => $bookingId]);

                $booking = $this->Bookings->patchEntity($booking, [
                    'customer_id' => $data['customer_id'],
                    'booking_name' => $data['booking_name'],
                    'booking_date' => $data['booking_date_formatted'],
                    'total_cost' => $data['total_cost'],
                    'remaining_cost' => $data['remaining_cost'],
                    'refund_due_amount' => $data['refund_due_amount'], 
                    'notes' => $data['notes'],
                 ], [
                     'associated' => [],
                     'guard' => false
                 ]);

                 if (!$this->Bookings->save($booking)) {
                    Log::error('[CustomerEdit] Failed to save main booking updates. Errors: ' . json_encode($booking->getErrors()));
                    throw new Exception('Failed to save main booking updates.');
                }

                $allServicesTimes = [];
                $servicesDetails = [];

                if ($hasServiceData) {
                    $serviceIds = array_column($data['bookings_services'], 'service_id');
                    if (!empty($serviceIds)) {
                        $servicesDetails = $this->Services->find('list', [
                            'keyField' => 'id',
                            'valueField' => 'duration_minutes',
                        ])->where(['id IN' => $serviceIds])->toArray();
                    }

                    $stylistTimeSlotsEdit = [];
                    $hasConflictEdit = false;
                    $conflictMessageEdit = '';

                    foreach ($data['bookings_services'] as $serviceData) {
                         if (!isset($serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_id'])) {
                             Log::warning('[CustomerEdit] Skipping service data due to missing fields: ' . json_encode($serviceData));
                             continue;
                         }
                         $stylistIdEdit = (int)$serviceData['stylist_id'];
                         $serviceIdEdit = (int)$serviceData['service_id'];
                         $startTimeStrEdit = $serviceData['start_time'];
                         $bookingDateStrEdit = $data['booking_date_formatted'];
                         $durationEdit = $servicesDetails[$serviceIdEdit] ?? 0;

                         if ($durationEdit <= 0) {
                             Log::warning("[CustomerEdit] Service ID {$serviceIdEdit} has zero or invalid duration.");
                             continue;
                         }

                         try {
                              $startTimeEdit = new DateTime($bookingDateStrEdit . ' ' . $startTimeStrEdit);
                              $endTimeEdit = clone $startTimeEdit;
                              $endTimeEdit->modify("+{$durationEdit} minutes");
                              $newSlotEdit = ['start' => $startTimeEdit->getTimestamp(), 'end' => $endTimeEdit->getTimestamp()];

                             // Check for conflicts within the submitted services for the same stylist
                             if (isset($stylistTimeSlotsEdit[$stylistIdEdit])) {
                                 foreach ($stylistTimeSlotsEdit[$stylistIdEdit] as $existingSlotEdit) {
                                     if ($newSlotEdit['start'] < $existingSlotEdit['end'] && $newSlotEdit['end'] > $existingSlotEdit['start']) {
                                         $hasConflictEdit = true;
                                         $conflictMessageEdit = "Time conflict detected for stylist. Please ensure service times do not overlap.";
                                         Log::warning("[CustomerEdit] Internal conflict detected: " . $conflictMessageEdit);
                                         break 2;
                                     }
                                 }
                             }

                              if (!$this->checkSegmentAvailability($bookingDateStrEdit, $startTimeEdit->format('H:i:s'), $endTimeEdit->format('H:i:s'), $serviceIdEdit, $stylistIdEdit, $bookingId)) {
                                 $hasConflictEdit = true;
                                 $conflictMessageEdit = "The selected time slot for a service conflicts with another booking.";
                                 Log::warning("[CustomerEdit] External conflict detected via checkSegmentAvailability.");
                                 break;
                              }


                              $stylistTimeSlotsEdit[$stylistIdEdit][] = $newSlotEdit;

                         } catch (Exception $e) {
                              Log::error('[CustomerEdit] Validation time processing error: ' . $e->getMessage());
                              throw new Exception('Validation time processing error: ' . $e->getMessage());
                         }
                     }

                    if ($hasConflictEdit) {
                        throw new Exception($conflictMessageEdit ?: 'A time conflict was detected. Please adjust service times.');
                    }


                    // Re-Save BookingsServices records
                    foreach ($data['bookings_services'] as $serviceData) {
                        // Basic validation again
                        if (!isset($serviceData['service_id'], $serviceData['stylist_id'], $serviceData['start_time'], $serviceData['service_cost'])) {
                             Log::warning('[CustomerEdit] Post-validation: Skipping incomplete service data: ' . json_encode($serviceData));
                            continue;
                        }
                        $serviceId = (int)$serviceData['service_id'];
                        $startTimeString = $serviceData['start_time'];
                        $duration = $servicesDetails[$serviceId] ?? 0;

                        if ($duration <= 0) continue;

                        // Use the potentially updated date
                        $startTime = new DateTime($data['booking_date_formatted'] . ' ' . $startTimeString);
                        $endTime = clone $startTime;
                        $endTime->modify("+{$duration} minutes");

                        $bookingService = $bookingsServicesTable->newEntity([
                            'booking_id' => $bookingId,
                            'service_id' => $serviceId,
                            'stylist_id' => (int)$serviceData['stylist_id'],
                            'start_time' => $startTime->format('H:i:s'),
                            'end_time' => $endTime->format('H:i:s'),
                            'service_cost' => $serviceData['service_cost'],
                        ]);

                        if (!$bookingsServicesTable->save($bookingService)) {
                            Log::error('[CustomerEdit] Failed to save booking service. Errors: ' . json_encode($bookingService->getErrors()));
                            throw new \Exception('Failed to save updated booking service details.');
                        }
                         $allServicesTimes[] = ['start' => $startTime, 'end' => $endTime];
                    }
                }

                 $overallStartTime = null;
                 $overallEndTime = null;
                 if (!empty($allServicesTimes)) {
                     $startTimestamps = array_map(function($t) { return $t['start']->getTimestamp(); }, $allServicesTimes);
                     $endTimestamps = array_map(function($t) { return $t['end']->getTimestamp(); }, $allServicesTimes);

                     if (!empty($startTimestamps)) {
                         $minStartTs = min($startTimestamps);
                         $overallStartTime = (new DateTime())->setTimestamp($minStartTs);
                     }
                     if (!empty($endTimestamps)) {
                         $maxEndTs = max($endTimestamps);
                         $overallEndTime = (new DateTime())->setTimestamp($maxEndTs);
                     }
                 }

                 $booking = $this->Bookings->patchEntity($booking, [
                     'start_time' => $overallStartTime ? $overallStartTime->format('H:i:s') : null,
                     'end_time' => $overallEndTime ? $overallEndTime->format('H:i:s') : null
                 ]);

                 if (!$this->Bookings->save($booking)) {
                     Log::error('[CustomerEdit] Failed to save overall times. Errors: ' . json_encode($booking->getErrors()));
                     throw new Exception('Failed to save overall times on booking update.');
                 }


                // Re-Create BookingsStylists records (only if services were submitted)
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
                                 'selected_cost' => $booking->total_cost,
                            ]);
                            if (!$bookingsStylistsTable->save($bookingStylist)) {
                                Log::error('[CustomerEdit] Failed to save booking stylist. Errors: ' . json_encode($bookingStylist->getErrors()));
                                 throw new Exception('Failed to save updated booking stylist details.');
                            }
                             $processedStylists[] = $stylistId;
                        }
                    }
                }

                $connection->commit();
                $this->Flash->success(__('Your booking has been updated successfully.'));

                $referer = $this->request->getHeaderLine('Referer');
                if ($id && !empty($referer) && strpos($referer, 'bookings/customerview') !== false) {
                    return $this->redirect(['controller' => 'Bookings', 'action' => 'customerview', $id]);
                } elseif (!empty($referer) && strpos($referer, 'customers/dashboard') !== false) {
                    return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']);
                }
                return $this->redirect(['controller' => 'Customers', 'action' => 'dashboard']); // Default

            } catch (Exception $e) {
                $connection->rollback();
                Log::error('[CustomerEdit] Booking update failed: ' . $e->getMessage() . ' Booking ID: ' . $bookingId . ' Data: ' . json_encode($data));
                $this->Flash->error(__('The booking could not be updated. Please, try again. Error: {0}', $e->getMessage()));
                 $booking->setError('general', $e->getMessage());
            }

        }

        $customers = $this->Bookings->Customers->find('list', ['limit' => 1])->where(['id' => $currentUserId])->all();
        $stylists = $this->Bookings->Stylists->find('list', limit: 200)->all();
        $services = $this->fetchTable('Services')->find('all')->all();

        $this->set(compact('booking', 'stylists', 'services', 'customers'));

        try {
             $this->render('customeredit');
        } catch (MissingTemplateException $e) {
             Log::warning('customeredit.php template not found, falling back to edit.php');
             $this->render('edit');
        }
    }

    public function viewPendingGuestBooking($token = null)
    {
        $bookingData = $this->request->getSession()->read('GuestBooking.pending_details');

        if (!$bookingData) {
            $this->Flash->error(__('No pending booking found in your session. It may have expired or been completed. Please start a new booking if needed.'));
            return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
        }

        // If a token is passed in the URL, validate it against the token in the session
        if ($token !== null) {
            if (!isset($bookingData['pending_booking_token']) || $bookingData['pending_booking_token'] !== $token) {
                $this->Flash->error(__('The booking link used is invalid or does not match your current pending booking. Please start a new booking if needed.'));
                return $this->redirect(['controller' => 'Bookings', 'action' => 'guestbooking']);
            }
        } elseif (isset($bookingData['pending_booking_token'])) {
            return $this->redirect(['controller' => 'Bookings', 'action' => 'viewPendingGuestBooking', $bookingData['pending_booking_token']]);
        }

        $this->set(compact('bookingData'));
    }

    /**
     * Marks a pending refund as processed by the admin.
     *
     * @param string|null $id Booking id.
     * @return \Cake\Http\Response|null|void Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function markRefundProcessed($id = null)
    {
        // Check if user is admin
        $user = $this->Authentication->getIdentity();
        if (!$user || $user->type !== 'admin') {
            $this->Flash->error('Access denied. Admin only area.');
            return $this->redirect(['action' => 'customerindex']);
        }

        $this->request->allowMethod(['post']);
        $booking = $this->Bookings->get($id, ['contain' => ['Customers']]); // Ensure Customer is loaded

        if ($booking->refund_due_amount > 0) {
            $booking->refund_due_amount = 0.00;
            if ($this->Bookings->save($booking)) {
                $customerName = $booking->has('customer') && $booking->customer ? $booking->customer->first_name . ' ' . $booking->customer->last_name : 'the customer';
                $this->Flash->success(__('Refund for booking #{0} for {1} has been marked as processed.', $booking->id, $customerName));
            } else {
                Log::error('Admin: Failed to mark refund as processed for booking ID ' . $id . '. Errors: ' . json_encode($booking->getErrors()));
                $this->Flash->error(__('Could not mark refund as processed. Please, try again.'));
            }
        } else {
            $this->Flash->info(__('No pending refund found for this booking.'));
        }

        return $this->redirect(['action' => 'index']);
    }

}
