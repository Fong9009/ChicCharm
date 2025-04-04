<?php
/**
 * Contact Reply text email template
 *
 * @var \App\View\AppView $this
 * @var string $first_name email recipient's first name
 * @var string $last_name email recipient's last name
 * @var string $message the reply message
 */
?>
REPLY FROM CHICCHARM
===================

Dear <?= h($first_name) ?> <?= h($last_name) ?>,

Thank you for contacting ChicCharm. Here is our reply:

<?= h($message) ?>

If you have any further questions, please don't hesitate to contact us again.

Best regards,
The ChicCharm Team

-----------------------------------------------------------
© <?= date('Y') ?> ChicCharm. All rights reserved. 