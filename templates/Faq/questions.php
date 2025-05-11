<?php
/**FAQ page template */
?>
<?= $this->Html->css('faq') ?>
<?= $this->Html->script('/js/faq.js') ?>
<div class="customer-dashboard" style="background-image: url(<?= $this->Url->image('gradient.jpg')?>);">
<div class="faq-container">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <div class = "title">
        <h2 class = "faq-header"><?= $this->ContentBlock->text('faq-title'); ?></h2>
            <p><?= $this->ContentBlock->text('faq-preamble'); ?></p>
    </div>

    <div class ="faq">
        <div class ="question">
            <h3>Question 1: <?= $this->ContentBlock->text('faq-question-title-1'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-1'); ?></p>
        </div>
        <hr>
    </div>

    <div class ="faq">
        <div class = "question">
            <h3>Question 2: <?= $this->ContentBlock->text('faq-question-title-2'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-2'); ?></p>
        </div>
        <hr>
    </div>

    <div class ="faq">
        <div class =  "question">
            <h3>Question 3: <?= $this->ContentBlock->text('faq-question-title-3'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-3'); ?></p>
        </div>
        <hr>
    </div>

    <div class ="faq">
        <div class ="question">
            <h3>Question 4: <?= $this->ContentBlock->text('faq-question-title-4'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-4'); ?></p>
        </div>
        <hr>
    </div>

    <div class ="faq">
        <div class ="question">
            <h3>Question 5: <?= $this->ContentBlock->text('faq-question-title-5'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-5'); ?></p>
        </div>
        <hr>
    </div>

    <div class ="faq">
        <div class ="question">
            <h3>Question 6: <?= $this->ContentBlock->text('faq-question-title-6'); ?></h3>
            <svg width = "15" height = "10" viewBox="0 0 42 25">
                <path d="M3 3L21 21L39 3" stroke="white" stroke-width="1" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="answer">
            <p><?= $this->ContentBlock->text('faq-question-desc-6'); ?></p>
        </div>
        <hr>
    </div>
</div>
</div>
