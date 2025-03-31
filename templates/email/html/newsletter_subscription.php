<?php
/**
 * Newsletter subscription HTML email template
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Welcome to ChicCharm Newsletter</title>
    <?= $this->Html->css('custom', ['fullBase' => true]) ?>
</head>
<body class="email-newsletter">
    <div class="container">
        <div class="header">
            <h1>Welcome to ChicCharm Newsletter!</h1>
        </div>
        <div class="content">
            <p>Dear <?= isset($name) ? h($name) : 'Valued Customer' ?>,</p>
            
            <p>Thank you for subscribing to the ChicCharm newsletter!</p>
            
            <p>You'll now receive the latest updates about:</p>
            <ul>
                <li>New makeup and hair styling services</li>
                <li>Special promotions and discounts</li>
                <li>Seasonal sales and events</li>
                <li>Styling tips and industry trends</li>
                <li>Behind-the-scenes looks at our creative process</li>
            </ul>
            
            <p>Stay connected with us on social media for daily inspiration:</p>
            <div class="social">
                <a href="https://facebook.com">Facebook</a>
                <a href="https://instagram.com">Instagram</a>
                <a href="https://youtube.com">YouTube</a>
            </div>
            
            <p>If you have any questions or feedback, feel free to reply to this email or contact us through our website.</p>
            
            <p><a href="<?= isset($websiteUrl) ? $websiteUrl : 'https://chiccharm.com' ?>" class="button">Visit Our Website</a></p>
            
            <p>Warm regards,<br>The ChicCharm Team</p>
        </div>
        <div class="footer">
            <p>© <?= date('Y') ?> ChicCharm. All rights reserved.</p>
            <p>
                If you no longer wish to receive our newsletters, you can 
                <a href="<?= isset($unsubscribeUrl) ? $unsubscribeUrl : '#' ?>">unsubscribe here</a>.
            </p>
        </div>
    </div>
</body>
</html> 