<?php

namespace App\Model\Behavior;

use Cake\Core\Exception;
use Cake\ORM\Behavior;
use Cake\ORM\Table;

class CanAuthenticateBehavior extends Behavior
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $emailColumn = $this->table()->getSchema()->getColumn('email');
        $passwordColumn = $this->table()->getSchema()->getColumn('password');
        $nonceColumn = $this->table()->getSchema()->getColumn('nonce');
        $nonceExpiryColumn = $this->table()->getSchema()->getColumn('nonce_expiry');

        if (!$emailColumn) {
            throw new Exception('Admins table does not contain an "email" column.');
        }

        if (!$passwordColumn) {
            throw new Exception('Admins table does not contain a "password" column.');
        }

        if (!$nonceColumn) {
            throw new Exception('Admins table does not contain a "nonce" column.');
        }

        if (!$nonceExpiryColumn) {
            throw new Exception('Admins table does not contain a "nonce_expiry" column.');
        }
    }
} 