<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\PaypalWebhookEvent[] $events
 * @var array $stats
 */

$this->Html->scriptBlock("// Auto-refresh the page every 30 seconds\nsetInterval(function() {\n    window.location.reload();\n}, 30000);", ['block' => 'script']);
?>
<style>
    /* Dashboard Specific Styles */
    .paypal-dashboard-page .h1.h3 {
        margin-bottom: 2.25rem; 
    }

    .paypal-dashboard-page .card-header h6 {
        font-size: 1.1rem;
        margin-bottom: 0; 
    }
    .paypal-dashboard-page .card-header {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .paypal-dashboard-page .table th,
    .paypal-dashboard-page .table td {
        padding: 1rem 0.85rem; 
        vertical-align: middle;
    }

    .paypal-dashboard-page .table thead th {
        background-color: #f8f9fc;
    }

    .paypal-dashboard-page .badge {
        padding: 0.4em 0.65em;
        font-size: 0.8rem;
        font-weight: 500;
    }

    .paypal-dashboard-page .stats-card .card-body {
        padding: 1.5rem; 
    }
    .paypal-dashboard-page .stats-card .col-auto {
        padding-left: 0.5rem;
    }
    .paypal-dashboard-page .stats-card .text-xs {
        font-size: 0.8rem;
    }
    .paypal-dashboard-page .stats-card .h5 {
        font-size: 1.5rem;
    }
    .paypal-dashboard-page .stats-card .fa-2x {
        font-size: 2.25em;
    }
    .paypal-dashboard-page .table-responsive {
        margin-top: 0.5rem;
    }
    .paypal-dashboard-page .card.shadow .card-body {
        padding: 1.5rem; 
    }

</style>

<div class="container-fluid paypal-dashboard-page pt-4 px-4" style="margin-bottom: 120px; padding-bottom: 100px;">
    <h1 class="h3 mb-4 text-gray-800">PayPal Payment Dashboard</h1>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Revenue</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats->total_amount ?? 0, 2) ?> AUD
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Transactions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?= number_format($stats->total_transactions ?? 0) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2 stats-card">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Average Transaction</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                $<?= number_format($stats->avg_amount ?? 0, 2) ?> AUD
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">PayPal Transaction History</h6>
        </div>
        <div class="card-body">
            <?php if (!empty($events)): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>Received At</th>
                            <th>PayPal Event ID</th>
                            <th>Event Name</th>
                            <th>Summary</th>
                            <th>Amount</th>
                            <th>PayPal Status</th>
                            <th>Internal Status</th>
                            <th>Payer</th>
                            <th>Resource ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?= $event->received_at ? $event->received_at->format('Y-m-d H:i:s') : 'N/A' ?></td>
                            <td><small><?= h($event->paypal_event_id) ?></small></td>
                            <td><?= h($event->event_name) ?></td>
                            <td><?= h($event->summary) ?></td>
                            <td>
                                <?php if ($event->amount !== null): ?>
                                    <?= $this->Number->currency($event->amount, $event->currency ?? 'USD') ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $statusClass = 'secondary';
                                if ($event->paypal_resource_status === 'COMPLETED' || $event->paypal_resource_status === 'SUCCESS') {
                                    $statusClass = 'success';
                                } elseif (strpos(strtoupper((string)$event->paypal_resource_status), 'PENDING') !== false) {
                                    $statusClass = 'warning';
                                } elseif (strpos(strtoupper((string)$event->paypal_resource_status), 'FAILED') !== false || strpos(strtoupper((string)$event->paypal_resource_status), 'DENIED') !== false) {
                                    $statusClass = 'danger';
                                }
                                ?>
                                <span class="badge badge-<?= $statusClass ?>">
                                    <?= h(strtoupper((string)$event->paypal_resource_status)) ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-info"><?= h($event->status)?></span>
                            </td>
                            <td>
                                <?php if ($event->payer_name || $event->payer_email): ?>
                                    <?= h($event->payer_name) ?><br>
                                    <small class="text-muted"><?= h($event->payer_email) ?></small>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted"><?= h($event->resource_id) ?></small>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <div class="alert alert-info" role="alert">
                    No webhook events found.
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->append('script'); ?>
<script>
// Auto-refresh the page every 30 seconds
setInterval(function() {
    window.location.reload();
}, 30000);
</script>
<?php $this->end(); ?> 