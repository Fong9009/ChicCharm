<?php
/**
 * @var \App\View\AppView $this
 * @var iterable<\App\Model\Entity\PaymentHistory> $payments
 */
?>
<?= $this->Html->css('/utility/indexes/indexes.css') ?>
<?= $this->Html->css('https://fonts.googleapis.com/icon?family=Material+Icons') ?>
<div class="admin-background">
<div class="payments index content admin-border" style="margin-bottom: 120px; padding-bottom: 120px;">
    <aside class="column">
        <div class="side-nav">
            <h4 class="heading"><?= __('Actions') ?></h4>
            <div class="row gx-2">
                <div class="col-lg-4 col-md-6 col-sm-12 mb-3 side-nav-item">
                    <?= $this->Html->link(
                        '<div class="card h-100">
                        <div class="card-header dashboard-card-header d-flex justify-content-between align-items-center flex-wrap">
                            <h4 class="view-card-h4 mb-0 flex-grow-1 text-truncate">Dashboard</h4>
                            <i class="material-icons view-icon ms-2">person</i>
                        </div>
                        <div class="card-body dashboard-card-body"></div>
                        <div class="card-footer dashboard-card-footer">
                            <span class="mb-0 text-truncate ">Back To Dashboard</span>
                        </div>
                    </div>',
                        ['controller' => 'Admins', 'action' => 'dashboard'],
                        ['escape' => false, 'class' => 'card-link-wrapper d-block text-decoration-none']
                    ) ?>
                </div>
            </div>
        </div>
    </aside>
    <h3><?= __('Payment History / Receipts') ?></h3>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th><?= $this->Paginator->sort('payment_date', 'Date') ?></th>
                    <th><?= __('Customer') ?></th>
                    <th><?= $this->Paginator->sort('booking_id', 'Booking Name') ?></th>
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
                                echo $this->Html->link($payment->booking->booking_name, ['controller' => 'Bookings', 'action' => 'view', $payment->booking->id]);
                            } else {
                                echo $payment->booking->booking_name ? h($payment->booking->booking_name) : 'N/A';
                            } ?>
                        </td>
                        <td><?= $this->Number->currency($payment->payment_amount, $payment->payment_currency ?: 'AUD') ?></td>
                        <td><?= h($payment->payment_status) ?></td>
                        <td><?= h($payment->payment_method) ?></td>
                        <td><?= h($payment->paypal_transaction_id) ?></td>
                        <td>
                            <?php if (!empty($payment->invoice_pdf)): ?>
                                <a href="/<?= h($payment->invoice_pdf) ?>" target="_blank">Download/Check Invoice</a>
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
</div>
