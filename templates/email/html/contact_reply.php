<?php
/**
 * Contact Reply HTML email template
 *
 * @var \App\View\AppView $this
 * @var string $first_name email recipient's first name
 * @var string $last_name email recipient's last name
 * @var string $message the reply message
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Reply from ChicCharm</title>
    <?= $this->Html->css('custom', ['fullBase' => true]) ?>
</head>
<body class="email-reply">
    <div class="container">
        <div class="header">
            <h1>Reply from ChicCharm</h1>
        </div>
        <div class="content">
            <p>Dear <?= h($first_name) ?> <?= h($last_name) ?>,</p>
            
            <p>Thank you for contacting ChicCharm. Here is our reply:</p>
            
            <div class="message-content">
                <?= nl2br(h($message)) ?>
            </div>
            
            <p>If you have any further questions, please don't hesitate to contact us again.</p>
            
            <p>Best regards,<br>The ChicCharm Team</p>
        </div>
    </div>
</body>
</html> 