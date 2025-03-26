<?php
/**
 * Reset Password text email template
 *
 * @var \App\View\AppView $this
 * @var string $first_name email recipient's first name
 * @var string $last_name email recipient's last name
 * @var string $email email recipient's email address
 * @var string $nonce nonce used to reset the password
 * @var string $userType type of user (admin or customer)
 */
?>
Reset your account password
==========

Hi <?= h($first_name) ?>,

Thank you for your request to reset the password of your account on ChicCharm.

To reset your account password, use the following link to access the reset password page:
<?= $this->Url->build(['controller' => 'Auth', 'action' => 'resetPassword', $nonce, $userType], ['fullBase' => true]) ?>

This link will expire in 7 days for security reasons.

If you did not request this password reset, please ignore this email or contact support if you have concerns about your account security.

==========
This email is addressed to <?= $first_name ?>  <?= $last_name ?> <<?= $email ?>>
Please discard this email if it not meant for you

Copyright (c) <?= date("Y"); ?> ChicCharm
