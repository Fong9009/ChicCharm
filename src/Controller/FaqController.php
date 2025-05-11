<?php
declare(strict_types=1);

namespace App\Controller;

class FaqController extends AppController
{

    public function initialize(): void
    {
        parent::initialize();
        $this->loadComponent('Authentication.Authentication');
        $this->Authentication->allowUnauthenticated(['questions']);
    }
    public function questions(){
    }
}
