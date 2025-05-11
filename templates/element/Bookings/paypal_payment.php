<?php
/**
 * Generic PayPal payment element
 *
 * Expected variables:
 * @var string $paymentAmount The amount to be charged (e.g., '100.00'). Defaults to '0.00'.
 * @var string $currencyCode The currency code (e.g., 'AUD'). Defaults to 'AUD'.
 * @var string $finalSuccessUrl The fully pre-constructed success URL. Defaults to '#error-success-url-not-set'.
 * @var string $finalCancelUrl The fully pre-constructed cancel URL. Defaults to '#error-cancel-url-not-set'.
 */
$paypalConfig = \Cake\Core\Configure::read('PayPal');
$clientId = $paypalConfig['clientId'] ?? null;

// Ensure variables are set with defaults to avoid JS errors
$paymentAmount = $paymentAmount ?? '0.00';
$currencyCode = $currencyCode ?? 'AUD';
$finalSuccessUrl = $finalSuccessUrl ?? '#error-success-url-not-set';
$finalCancelUrl = $finalCancelUrl ?? '#error-cancel-url-not-set';

// Validate that essential URLs are not the default error ones if we are trying to render
$validUrls = ($finalSuccessUrl !== '#error-success-url-not-set' && $finalCancelUrl !== '#error-cancel-url-not-set');

?>

<div class="paypal-payment">
    <div id="paypal-button-container"></div>
    <p id="result-message" class="text-danger"></p>
</div>

<?php
if (empty($clientId)) {
    echo '<div class="alert alert-danger">PayPal Client ID is not configured. Payment cannot proceed.</div>';
} elseif (!$validUrls) {
    echo '<div class="alert alert-danger">PayPal Success/Cancel URLs are not correctly configured. Payment cannot proceed.</div>';
} else {
    // Use the $currencyCode variable in the SDK URL
    echo $this->Html->script(
        sprintf(
            'https://www.paypal.com/sdk/js?client-id=%s&currency=%s&components=buttons&enable-funding=venmo,paylater,card',
            h($clientId),
            h($currencyCode)
        ),
        ['block' => true]
    );
}
?>

<?php $this->append('script'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const payPalContainer = document.getElementById('paypal-button-container');
    const resultMessageContainer = document.getElementById('result-message');
    const clientIdIsConfigured = <?= !empty($clientId) && $validUrls ? 'true' : 'false' ?>;

    function displayError(message) {
        if (resultMessageContainer) {
            resultMessageContainer.innerHTML = message;
        }
        console.error(message);
    }

    if (!clientIdIsConfigured) {
        return;
    }

    if (!payPalContainer) {
        console.error('PayPal button container (paypal-button-container) not found on this page.');
        return;
    }

    if (typeof paypal === 'undefined') {
        displayError('Error: PayPal SDK not loaded. Please check your internet connection or client ID configuration.');
        return;
    }

    try {
        paypal.Buttons({
            style: {
                shape: "rect",
                layout: "vertical",
                color: "gold",
                label: "paypal",
            },
            createOrder: function(data, actions) {
                console.log('Client-side createOrder: Amount: <?= h($paymentAmount) ?>, Currency: <?= h($currencyCode) ?>');
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?= h($paymentAmount) ?>',
                            currency_code: 'AUD'
                        }
                    }],
                    application_context: {
                        shipping_preference: 'NO_SHIPPING',
                        user_country: 'AU'
                    }
                });
            },
            onApprove: function(data, actions) {
                console.log('Client-side onApprove. OrderID: ' + data.orderID);
                return actions.order.capture().then(function(details) {
                    console.log('Payment captured:', details);
                    const transactionId = details.id;
                    const payerId = details.payer && details.payer.payer_id ? details.payer.payer_id : null;
                    let successUrl = '<?= $finalSuccessUrl ?>';

                    successUrl += (successUrl.includes('?') ? '&' : '?') + `transaction_id=${transactionId}`;
                    if (payerId) {
                        successUrl += `&paypal_payer_id=\${payerId}`;
                    }
                    // Include the original PayPal Order ID for server-side reference if needed
                    successUrl += `&paypal_order_id=\${data.orderID}`;

                    window.location.href = successUrl;
                }).catch(function(err) {
                    console.error('Error during payment capture:', err);
                    displayError('An error occurred while capturing your payment. Please try again or contact support.');
                });
            },
            onCancel: function(data) {
                console.log('Payment cancelled. OrderID: ' + (data && data.orderID ? data.orderID : 'N/A'));
                let cancelUrl = '<?= $finalCancelUrl ?>';
                if (data && data.orderID) {
                    cancelUrl += (cancelUrl.includes('?') ? '&' : '?') + `paypal_order_id=\${data.orderID}`;
                }
                window.location.href = cancelUrl;
            },
            onError: function(err) {
                console.error('PayPal Buttons onError:', err);
                displayError('An error occurred with the PayPal payment process. Please try again or contact support.');
            }
        }).render('#' + payPalContainer.id);
    } catch (error) {
        console.error('Failed to render PayPal Buttons:', error);
        displayError('Could not initialize PayPal payment options. Please try refreshing the page.');
    }
});
</script>
<?php $this->end(); ?> 