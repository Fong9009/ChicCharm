<?php
/**
 * @var \App\View\AppView $this
 * @var string $recipientEmail
 * @var string $websiteUrl
 * @var string $unsubscribeUrl
 * @var string $companyName
 */
?>
Welcome to the <?= h($companyName) ?> Newsletter!
============================================

Hello,

Thank you for subscribing to our newsletter! You're now set to receive the latest news, updates, and special offers directly in your inbox.

We're excited to have you as part of the <?= h($companyName) ?> community.

You can visit our website anytime at: <?= h($websiteUrl) ?>

Stay tuned for our upcoming newsletters!

Best regards,

The <?= h($companyName) ?> Team

--------------------------------------------
You received this email because you subscribed to our newsletter with the email address: <?= h($recipientEmail) ?>

If you no longer wish to receive these emails, please use the following link to unsubscribe:
<?= h($unsubscribeUrl) ?>

© <?= date('Y') ?> <?= h($companyName) ?>. All rights reserved. 