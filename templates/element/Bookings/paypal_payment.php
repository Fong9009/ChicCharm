<?php
/**
 * PayPal payment element
 *
 * @var \App\Model\Entity\Booking $booking
 */
$paypalConfig = \Cake\Core\Configure::read('PayPal');
?>

<div class="paypal-payment">
    <div id="paypal-button-container"></div>
</div>

<script src="https://www.paypal.com/sdk/js?client-id=<?= $paypalConfig['clientId'] ?>&currency=AUD"></script>
<script>
    paypal.Buttons({
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: '<?= $booking->remaining_cost ?>',
                        currency_code: 'AUD'
                    }
                }]
            });
        },
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(details) {
                // Capture the transaction and payer IDs
                const transactionId = details.id;
                const payerId = details.payer.payer_id;

                // Build the base success URL
                let successUrl = '<?= $this->Url->build(['controller' => 'Payments', 'action' => 'success', $booking->id], ['fullBase' => true]) ?>';

                // Append the transaction ID as a query parameter
                successUrl += `?transaction_id=${transactionId}`;

                // Append the payer ID if it exists
                if (payerId) {
                    successUrl += `&payer_id=${payerId}`;
                }

                // Redirect to success page with the IDs
                window.location.href = successUrl;
            });
        },
        onCancel: function(data) {
            if (data.intent === 'cancel') {
                window.location.href = '<?= $this->Url->build(['controller' => 'Payments', 'action' => 'cancel', $booking->id]) ?>';
            }
        },
        onError: function(err) {
            console.error('PayPal error:', err);
            alert('An error occurred while processing your payment. Please try again.');
        }
    }).render('#paypal-button-container');
</script> 