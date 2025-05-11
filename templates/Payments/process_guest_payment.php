<?php
/**
 * @var \App\View\AppView $this
 * @var array $bookingData From session (contains details like total_cost, id which is temporary booking_id)
 * @var string $clientId PayPal Client ID
 * @var string $mode PayPal mode (sandbox or live)
 */

// Ensure bookingData and total_cost are available
if (empty($bookingData) || !isset($bookingData['total_cost'])) {
    echo '<p class="text-danger">Error: Booking details are missing. Cannot proceed with payment.</p>';
    echo $this->Html->link(
        __('Return to Booking Form'),
        ['controller' => 'Bookings', 'action' => 'guestbooking'],
        ['class' => 'btn btn-secondary mt-3']
    );
    return;
}

$totalCost = number_format((float)($bookingData['total_cost'] ?? 0), 2, '.', '');
$temporaryBookingId = $bookingData['id'] ?? 'temp_id_' . uniqid();

?>
<div class="login-wrapper">
  <div class="container mt-5 payments process-guest-payment">
      <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6">
              <div class="card">
                  <div class="card-header">
                      <h3 class="text-center"><?= __('Confirm Your Booking & Pay') ?></h3>
                  </div>
                  <div class="card-body">
                      <h4 class="mb-3">Booking Summary</h4>
                      <p><strong>Total Amount Due:</strong> AUD $<?= h($totalCost) ?></p>

                      <?php if (isset($bookingData['booking_date'])) : ?>
                          <p><strong>Date:</strong> <?= h((new \Cake\I18n\FrozenDate($bookingData['booking_date']))->format('d/m/Y')) ?></p>
                      <?php endif; ?>

                      <?php if (!empty($bookingData['bookings_services_summary'])): ?>
                          <h5 class="mt-4 mb-2">Selected Services:</h5>
                          <ul class="list-unstyled">
                              <?php foreach ($bookingData['bookings_services_summary'] as $item): ?>
                                  <li class="mb-2">
                                      <strong><?= h($item['service_name']) ?></strong>
                                      <?php if (!empty($item['stylist_name']) && $item['stylist_name'] !== 'Unknown Stylist'): ?>
                                          with <?= h($item['stylist_name']) ?>
                                      <?php endif; ?>
                                      <?php if (isset($item['start_time_formatted'])): ?>
                                          at <?= h((new \Cake\I18n\FrozenTime($item['start_time_formatted']))->format('h:i A')) ?>
                                      <?php endif; ?>
                                      (<?= $this->Number->currency($item['service_cost']) ?>)
                                  </li>
                              <?php endforeach; ?>
                          </ul>
                      <?php endif; ?>

                      <hr class="my-4">

                      <h5 class="mb-3">Payment</h5>
                      <p>Please complete your payment using PayPal to confirm your booking.</p>

                      <div id="paypal-button-container" style="max-width: 400px; margin: 20px auto;"></div>

                      <?php
                        // Render the PayPal payment element
                        echo $this->element('Bookings/paypal_payment', compact(
                            'paymentAmount',
                            'currencyCode',
                            'finalSuccessUrl',
                            'finalCancelUrl'
                        ));
                      ?>

                      <p class="text-center text-muted small mt-3">
                          You will be redirected to PayPal to complete your payment securely.
                      </p>
                  </div>
                  <div class="card-footer text-center">
                       <?= $this->Html->link(
                          __('Cancel and Return to Booking Form'),
                          ['controller' => 'Bookings', 'action' => 'guestbooking'],
                          ['class' => 'btn btn-outline-secondary btn-sm']
                      ) ?>
                  </div>
              </div>
          </div>
      </div>
  </div>
</div> 

<?php $this->end(); ?>