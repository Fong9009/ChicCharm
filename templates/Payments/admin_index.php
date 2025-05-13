<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PaymentHistory> $payments
 */
?>
<div class="payments index content" style="margin-bottom: 120px; padding-bottom: 120px;">
    <h3><?= __('Payment History / Receipts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('id', 'Payment ID') ?></th>
                    <th><?= $this->Paginator->sort('payment_date', 'Date') ?></th>
                    <th><?= __('Customer') ?></th>
                    <th><?= $this->Paginator->sort('booking_id', 'Booking ID') ?></th>
                    <th><?= $this->Paginator->sort('payment_amount', 'Amount') ?></th>
                    <th><?= $this->Paginator->sort('payment_status', 'Status') ?></th>
                    <th><?= $this->Paginator->sort('payment_method', 'Method') ?></th>
                    <th><?= $this->Paginator->sort('paypal_transaction_id', 'Transaction ID') ?></th>
                    <th><?= __('Invoice') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments->toArray())): ?>
                    <?php foreach ($payments as $payment): ?>
                    <tr>
                        <td><?= $this->Number->format($payment->id) ?></td>
                        <td><?= h($payment->payment_date ? $payment->payment_date->format('Y-m-d H:i:s') : 'N/A') ?></td>
                        <td>
                            <?php
                            if (
                                $payment->hasValue('booking') &&
                                $payment->booking &&
                                $payment->booking->hasValue('customer') &&
                                $payment->booking->customer
                            ) {
                                echo h($payment->booking->customer->first_name) . ' ' . h($payment->booking->customer->last_name);
                            } elseif ($payment->hasValue('booking') && $payment->booking && !empty($payment->booking->booking_name)) {
                                echo h($payment->booking->booking_name);
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                            <?php // Check if booking relationship is loaded and not null
                            if ($payment->hasValue('booking') && $payment->booking) {
                                echo $this->Html->link($payment->booking_id, ['controller' => 'Bookings', 'action' => 'view', $payment->booking->id]);
                            } else {
                                echo $payment->booking_id ? h($payment->booking_id) : 'N/A';
                            } ?>
                        </td>
                        <td><?= $this->Number->currency($payment->payment_amount, $payment->payment_currency ?: 'AUD') ?></td>
                        <td><?= h($payment->payment_status) ?></td>
                        <td><?= h($payment->payment_method) ?></td>
                        <td><?= h($payment->paypal_transaction_id) ?></td>
                        <td>
                            <?php if (!empty($payment->invoice_pdf)): ?>
                                <a href="/<?= h($payment->invoice_pdf) ?>" target="_blank">Check/Download Invoice</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12"><?= __('No payment history found.') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="paginator">
        <ul class="pagination">
            <?= $this->Paginator->first('<< ' . __('first')) ?>
            <?= $this->Paginator->prev('< ' . __('previous')) ?>
            <?= $this->Paginator->numbers() ?>
            <?= $this->Paginator->next(__('next') . ' >') ?>
            <?= $this->Paginator->last(__('last') . ' >>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} record(s) out of {{count}} total')) ?></p>
    </div>
</div> 