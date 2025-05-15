<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Service[] $services
 */
use Cake\Routing\Router;
$identity = $this->request->getAttribute('identity');
$bookingButton = $this->ContentBlock->text('booking-button');
// Set up the booking link based on user type
if ($identity) {
    if ($identity->type === 'customer') {
        $link = ['controller' => 'Bookings', 'action' => 'customerbooking'];
    } elseif ($identity->type === 'admin') {
        $link = ['controller' => 'Bookings', 'action' => 'adminbooking'];
    } elseif ($identity->type === 'stylist') {
        $link = ['controller' => 'Stylists', 'action' => 'dashboard'];
    } else {
        $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
    }
} else {
    $link = ['controller' => 'Bookings', 'action' => 'guestbooking'];
}
?>
<div style="background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url(<?= $this->Url->image('servicepage.jpg')?>) center center / cover no-repeat;">
    <div class="row p-2 justify-content-center">
        <div class="col-12 mt-5 text-center">
            <h1 class="fw-bold text-white"><?= $this->ContentBlock->text('service-page-title'); ?></h1>
            <h2 class="fw-bold text-white"> <?= $this->ContentBlock->text('service-page-description'); ?></h2>
            <h3 class="text-white">Made your choice?</h3>
            <a> <?= $this->Html->link($bookingButton, $link, ['class' => 'btn btn-primary btn-xl', 'onclick' => 'handleBookingClick(event)', 'style' =>"background-color: orange"]) ?></a>
        </div>
    </div>
    <div class="row p-2 justify-content-center">
        <div class="col-lg-4 col-md-12 col-sm-12">
            <?= $this->Form->create(null, ['type' => 'get', 'class' => 'search-form']) ?>
            <div class="justify-content-center">
                <?= $this->Form->control('search', [
                    'label' => false,
                    'placeholder' => 'Search...',
                    'class' => 'form-control',
                    'value' => $this->request->getQuery('search'),
                ]) ?>
            </div>
            <?= $this->Form->end() ?>
        </div>
        <div class="col-12 text-center p-2">
            <h5 class="text-white"> Can't find what you are looking for? <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'enquiry'])?>">
                    <span>Contact Us</span>
                </a></h5>
        </div>
    </div>
    <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
</div>


<section class="px-5 mt-4">
    <?php
    $counter = 0;
    foreach ($services as $service):
        $imagePath = $service->service_image;
        if ($imagePath != null) {
           $imagePath = Router::url('/img/service/' . $service->service_image, true);
        } else {
            $imagePath = Router::url('/img/service/service-placeholder.jpg', true);
        }
        if ($counter % 3 === 0): ?>
            <div class="row justify-content-center">
        <?php endif; ?>

        <div class="col-lg-4 mb-4">
            <div class="card h-100 fade-in-title">
                <div class="card-header d-flex justify-content-between align-items-center"
                     style="background: url('<?= $imagePath ?>') center center / cover no-repeat; height: 150px;">
                    <div>
                        <h4 class="admin-card-h4 text-white bg-dark bg-opacity-50 p-1 rounded"><strong><?= h($service->service_name) ?></strong></h4>
                    </div>
                </div>
                <div class="card-body">
                    <h5 style="font-family: Raleway, sans-serif;"><?= h($service->service_desc) ?></h5>
                    <p><strong>Cost:</strong> $<?= h($service->service_cost) ?></p>
                </div>
                <div class="card-footer" style="background-color: orange">
                    <?= $this->Html->link('View Service', ['controller' => 'Services', 'action' => 'serviceView', $service->id], ['class' => 'btn btn-primary w-100', 'style' =>"background-color: orange"]) ?>
                </div>
            </div>
        </div>

        <?php
        $counter++;
        if ($counter % 3 === 0): ?>
            </div>
        <?php endif;
    endforeach;

    // Close row if last row has less than 3 cards
    if ($counter % 3 !== 0): ?>
    <?php endif; ?>
    <?php if (!empty($services)) : ?>
        <?php if (count($services) > 6) : ?>
            <div class="paginator text-center p-2" style="margin-bottom: 130px; margin-top: 130px;">
                <ul class="pagination justify-content-center">
                    <?= $this->Paginator->first('<<') ?>
                    <?= $this->Paginator->prev('<') ?>
                    <?= $this->Paginator->next('>') ?>
                    <?= $this->Paginator->last('>>') ?>
                </ul>
                <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} Services out of {{count}} total')) ?></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
