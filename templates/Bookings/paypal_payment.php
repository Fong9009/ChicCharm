<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Booking $booking
 */
?>
<div class="paypal-payment">
    <div id="paypal-button-container"></div>
    <p id="result-message"></p>
</div>

<?php 
$clientId = Configure::read('PayPal.clientId');
if (empty($clientId)) {
    echo '<div class="alert alert-danger">PayPal client ID is not configured. Please check your app_local.php settings.</div>';
} else {
    echo $this->Html->script('https://www.paypal.com/sdk/js?client-id=' . $clientId . '&currency=AUD&components=buttons&enable-funding=venmo,paylater,card', ['block' => true]);
}
?>

<?php $this->append('script'); ?>
<script>
    console.log('PayPal script loaded');
    
    if (typeof paypal === 'undefined') {
        console.error('PayPal SDK not loaded');
        document.getElementById('result-message').innerHTML = 'Error: PayPal SDK not loaded. Please check your client ID.';
    } else {
        console.log('Initializing PayPal buttons');
        const paypalButtons = window.paypal.Buttons({
            style: {
                shape: "rect",
                layout: "vertical",
                color: "gold",
                label: "paypal",
            },
            async createOrder() {
                try {
                    console.log('Creating order for booking:', <?= $booking->id ?>);
                    const response = await fetch("<?= $this->Url->build(['controller' => 'Payments', 'action' => 'createOrder', $booking->id]) ?>", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                        },
                    });

                    const orderData = await response.json();
                    console.log('Order response:', orderData);

                    if (orderData.id) {
                        return orderData.id;
                    }
                    const errorDetail = orderData?.details?.[0];
                    const errorMessage = errorDetail
                        ? `${errorDetail.issue} ${errorDetail.description} (${orderData.debug_id})`
                        : JSON.stringify(orderData);

                    throw new Error(errorMessage);
                } catch (error) {
                    console.error('Create order error:', error);
                    resultMessage(`Could not initiate PayPal Checkout...<br><br>${error}`);
                }
            },
            async onApprove(data, actions) {
                try {
                    console.log('Approving order:', data.orderID);
                    const response = await fetch(
                        "<?= $this->Url->build(['controller' => 'Payments', 'action' => 'captureOrder', $booking->id]) ?>",
                        {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                            },
                            body: JSON.stringify({
                                orderID: data.orderID
                            })
                        }
                    );

                    const orderData = await response.json();
                    console.log('Capture response:', orderData);
                    const errorDetail = orderData?.details?.[0];

                    if (errorDetail?.issue === "INSTRUMENT_DECLINED") {
                        return actions.restart();
                    } else if (errorDetail) {
                        throw new Error(`${errorDetail.description} (${orderData.debug_id})`);
                    } else if (!orderData.purchase_units) {
                        throw new Error(JSON.stringify(orderData));
                    } else {
                        window.location.href = "<?= $this->Url->build(['controller' => 'Payments', 'action' => 'success', $booking->id]) ?>";
                    }
                } catch (error) {
                    console.error('Capture order error:', error);
                    resultMessage(`Sorry, your transaction could not be processed...<br><br>${error}`);
                }
            },
        });

        paypalButtons.render("#paypal-button-container");
    }

    function resultMessage(message) {
        const container = document.querySelector("#result-message");
        container.innerHTML = message;
    }
</script>
<?php $this->end(); ?> 