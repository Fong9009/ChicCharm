<?php
/**
 * Newsletter subscription text email template
 */
?>
WELCOME TO CHICCHARM NEWSLETTER!
================================

Dear <?= isset($name) ? $name : 'Valued Customer' ?>,

Thank you for subscribing to the ChicCharm newsletter!

You'll now receive the latest updates about:
- New makeup and hair styling services
- Special promotions and discounts
- Seasonal sales and events
- Styling tips and industry trends
- Behind-the-scenes looks at our creative process

Stay connected with us on social media for daily inspiration:
Facebook: https://facebook.com
Instagram: https://instagram.com
YouTube: https://youtube.com

If you have any questions or feedback, feel free to reply to this email or contact us through our website.

Visit our website: <?= isset($websiteUrl) ? $websiteUrl : 'https://chiccharm.com' ?>

Warm regards,
The ChicCharm Team

-----------------------------------------------------------
© <?= date('Y') ?> ChicCharm. All rights reserved.

If you no longer wish to receive our newsletters, you can unsubscribe here:
<?= isset($unsubscribeUrl) ? $unsubscribeUrl : 'https://chiccharm.com/unsubscribe' ?> 