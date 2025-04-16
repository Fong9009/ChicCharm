<?php
/**
 * @var \App\View\AppView $this
 * @var string $message
 * @var string $url
 */
use Cake\Core\Configure;

$this->layout = 'error';

if (Configure::read('debug')) :
    $this->layout = 'dev_error';

    $this->assign('title', $message);
    $this->assign('templateName', 'error400.php');

    $this->start('file');
    echo $this->element('auto_table_warning');
    $this->end();
endif;
?>

<div class="error-content">
    <h1 class="error-code">404</h1>
    <h2 class="error-title">Not Found</h2>
    <p class="error-message">
        The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.
    </p>
    <div class="error-actions">
        <a href="javascript:history.back()" class="error-btn primary">
            Go Back
        </a>
        <a href="/" class="error-btn secondary">
            Go to Homepage
        </a>
    </div>
</div>

<style>
    .error-container {
        background-image: url('<?= $this->Url->image('bg-mastheadv2.jpg')?>');
        background-size: cover;
        background-position: center;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .error-content {
        max-width: 600px;
        padding: 2rem;
        margin: 0 auto; 
        text-align: center;
    }

    .error-code {
        font-size: 6rem;
        font-weight: bold;
        color: #c99863;
        margin: 0;
        line-height: 1;
    }

    .error-title {
        font-size: 2rem;
        color:rgb(230, 230, 230);
        margin: 1rem 0;
    }

    .error-message {
        color:rgb(230, 230, 230);
        margin-bottom: 2rem;
    }

    .error-actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
    }

    .error-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 5px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .primary {
        background-color: #c99863;
        color: white;
        border: none;
    }

    .primary:hover {
        background-color: #b8864f;
        color: white;
    }

    .secondary {
        background-color: #6c757d;
        color: white;
        border: none;
    }

    .secondary:hover {
        background-color: #5a6268;
        color: white;
    }
</style> 