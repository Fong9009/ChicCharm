<?php
/**
 * Reset Password HTML email template
 *
 * @var \App\View\AppView $this
 * @var string $first_name email recipient's first name
 * @var string $last_name email recipient's last name
 * @var string $email email recipient's email address
 * @var string $nonce nonce used to reset the password
 * @var string $userType type of user (admin or customer)
 */
?>
<div class="content">
    <!-- START CENTERED WHITE CONTAINER -->
    <table role="presentation" class="main">
        <!-- START MAIN CONTENT AREA -->
        <tr>
            <td class="wrapper">
                <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            <h3>Reset your account password</h3>
                            <p>Hi <?= h($first_name) ?>, </p>
                            <p>Thank you for your request to reset the password of your account on <b>ChicCharm</b>. </p>
                            <p></p>
                            <p>To reset your account password, use the button below to access the reset password page: </p>
                            <table role="presentation" border="0" cellpadding="0" cellspacing="0" class="btn btn-primary">
                                <tbody>
                                <tr>
                                    <td align="left">
                                        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                            <tr>
                                                <td><a href="<?= $this->Url->build(['controller' => 'Auth', 'action' => 'resetPassword', $nonce, $userType], ['fullBase' => true]) ?>" target="_blank">Reset account password</a></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            <p>or use the following link: <br>
                                <?= $this->Html->link($this->Url->build(['controller' => 'Auth', 'action' => 'resetPassword', $nonce, $userType], ['fullBase' => true]), ['controller' => 'Auth', 'action' => 'resetPassword', $nonce, $userType], ['fullBase' => true, 'style' => 'word-break:break-all']) ?></p>
                            
                            <p>This link will expire in 7 days for security reasons.</p>
                            
                            <p>If you did not request this password reset, please ignore this email or contact support if you have concerns about your account security.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <!-- END MAIN CONTENT AREA -->
    </table>
    <!-- END CENTERED WHITE CONTAINER -->
    <!-- START FOOTER -->
    <div class="footer">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td class="content-block">
                    This email is addressed to <?= $first_name ?>  <?= $last_name ?> &lt;<?= $email ?>&gt;<br>
                    Please discard this email if it not meant for you
                    <br>
                    <br>
                    Copyright &copy; <?= date("Y"); ?> ChicCharm
                </td>
            </tr>
        </table>
    </div>
    <!-- END FOOTER -->
</div>
