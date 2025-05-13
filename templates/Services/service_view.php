<?php
/**
 * @var \App\View\AppView $this
 * @var \App\Model\Entity\Stylist[] $stylists
 * @var \App\Model\Entity\Service[] $serviceInQuestion
 * @var int $currentServiceId
 */
use Cake\Routing\Router;
$imageServicePath = $serviceInQuestion->service_image;
if ($imageServicePath != null) {
    $imageServicePath = Router::url('/img/service/' . $serviceInQuestion->service_image, true);
} else {
    $imageServicePath = Router::url('/img/service/service-placeholder.jpg', true);
}
?>
<section class="px-5 mt-4">
    <div style=" background: url('<?= $imageServicePath ?>')  center center / cover no-repeat; " >
        <div class="row p-2 justify-content-start">
            <div class="col-lg-2 text-center">
                <?= $this->Html->link('Back to Services', ['controller' => 'Services', 'action' => 'servicePage'], ['class' => 'btn btn-primary w-100', 'style' =>"background-color: orange"]) ?>
            </div>
        </div>
        <div class="row p-2 justify-content-center">
            <div class="col-lg-8 text-center" style="background-color: rgba(0, 0, 0, 0.5); padding: 20px;">
                <h1 class="fw-bold text-white"><?= $this->ContentBlock->text('service-page-stylist-title'); ?></h1>
                <h1 class="fw-bold text-white"><?= $serviceInQuestion->service_name ?></h1>
                <h2 class="text-white"> <?= $this->ContentBlock->text('service-page-stylist-description'); ?></h2>
            </div>
        </div>
        <div class="row p-2 justify-content-center">
            <div class="col-lg-6 col-md-12 col-sm-12" style="background-color: rgba(0, 0, 0, 0.5); padding: 20px;">
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
                <div class="col-12 text-center p-2">
                    <h5 class="text-white"> Can't find who you are looking for? <a href="<?= $this->Url->build(['controller' => 'Contacts', 'action' => 'enquiry'])?>">
                            <span>Contact Us</span>
                        </a></h5>
                </div>
            </div>
        </div>
        <hr class="flex-grow-1 mx-auto" style="border: none; height: 3px; background-color: #c99863;"/>
    </div>
    <div class="card h-100 fade-in-title mb-3" style=" box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3);">
        <div class="row g-0 h-100">
            <div class="card-body text-center" style="background-color: #1e2125;">
                <h1 class="fw-bold text-white mb-3"><?= $serviceInQuestion->service_name ?></h1>
                <h3 class="fw-bold text-white mb-3">"<?= $serviceInQuestion->service_desc ?>"</h3>
                <h3 class="fw-bold text-white mb-0">$<?= $serviceInQuestion->service_cost ?> </h3>
                <h3 class="fw-bold text-white mb-0">Stylists Who do <?=$serviceInQuestion->service_name ?></h3>
            </div>
        </div>
        <div class="card-footer" style="background-color: orange;"></div>
    </div>

    <?php
    $counter = 0;
    foreach ($stylists as $stylist):
        $imagePath = $stylist->profile_picture;
        if ($imagePath != null) {
            $imagePath = Router::url('/img/profile/' . $stylist->profile_picture, true);
        } else {
            $imagePath = Router::url('/img/profile/stylist-placeholder.jpg', true);
        }
        if ($counter % 2 === 0): ?>
            <div class="row justify-content-center">
        <?php endif; ?>

        <div class="col-lg-6 col-md-12 col-sm-12 mb-4">
            <div class="card h-100 fade-in-title">
                <div class="row g-0 h-100">
                    <!-- Left side: Image -->
                    <div class="col-md-5">
                        <div class="position-relative w-100 h-100" style="min-height: 500px; background: url('<?= $imagePath ?>') center center / cover no-repeat;">
                            <div class="position-absolute bottom-0 start-0 end-0 text-center bg-dark bg-opacity-50 text-white p-2">
                                <strong>"<?= h($stylist->stylist_motto) ?>"</strong>
                            </div>
                        </div>
                    </div>

                    <!-- Right side: Text -->
                    <div class="col-md-7 d-flex flex-column justify-content-center">
                        <div class="card-body">
                            <h1>Stylist Details</h1>
                            <hr style="border: none; height: 3px; background-color: #c99863;"/>
                            <h4><strong>Stylist Name:</strong> <?= h($stylist->first_name) . " " . h($stylist->last_name) ?></h4>
                            <h5><strong>Stylist Bio:</strong> <?= h($stylist->stylist_bio) ?></h5>
                            <h5><strong>Other Services that <?= h($stylist->first_name)?> Offers: </strong></h5>
                            <?php foreach ($stylist->services as $service): ?>
                                <?php if ($service->id != $currentServiceId ): ?>
                                    <li><?= h($service->service_name) ?></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <div class="card-footer" style="background-color: orange;"></div>
            </div>
        </div>
        <?php
        $counter++;
        if ($counter % 2 === 0): ?>
            </div>
        <?php endif;
    endforeach;

    // Close row if last row has less than 3 cards
    if ($counter % 2 !== 0): ?>
        </div>
    <?php endif; ?>
    <div class="paginator text-center p-2">
        <ul class="pagination justify-content-center">
            <?= $this->Paginator->first('<<') ?>
            <?= $this->Paginator->prev('<') ?>
            <?= $this->Paginator->next('>') ?>
            <?= $this->Paginator->last('>>') ?>
        </ul>
        <p><?= $this->Paginator->counter(__('Page {{page}} of {{pages}}, showing {{current}} Services out of {{count}} total')) ?></p>
    </div>
</section>
