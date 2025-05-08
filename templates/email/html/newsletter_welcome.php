<?php
/**
 * @var \App\View\AppView $this
 * @var string $recipientEmail
 * @var string $websiteUrl
 * @var string $unsubscribeUrl
 * @var string $companyName
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Welcome to <?= h($companyName) ?> Newsletter</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .header { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee; }
        .content { margin-bottom: 20px; }
        .footer { text-align: center; font-size: 0.9em; color: #777; margin-top: 20px; padding-top: 10px; border-top: 1px solid #eee; }
        a { color: #d9534f; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to the <?= h($companyName) ?> Newsletter!</h1>
        </div>
        <div class="content">
            <p>Hello,</p>
            <p>Thank you for subscribing to our newsletter! You're now set to receive the latest news, updates, and special offers directly in your inbox.</p>
            <p>We're excited to have you as part of the <?= h($companyName) ?> community.</p>
            <p>You can visit our website anytime at: <a href="<?= h($websiteUrl) ?>"><?= h($websiteUrl) ?></a></p>
            <p>Stay tuned for our upcoming newsletters!</p>
            <p>Best regards,</p>
            <p>The <?= h($companyName) ?> Team</p>
        </div>
        <div class="footer">
            <p>You received this email because you subscribed to our newsletter with the email address: <?= h($recipientEmail) ?></p>
            <p>If you no longer wish to receive these emails, you can <a href="<?= h($unsubscribeUrl) ?>">unsubscribe here</a>.</p>
            <p>&copy; <?= date('Y') ?> <?= h($companyName) ?>. All rights reserved.</p>
        </div>
    </div>
</body>
</html> 