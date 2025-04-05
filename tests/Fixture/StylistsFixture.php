<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * StylistsFixture
 */
class StylistsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'first_name' => 'Lorem ipsum dolor sit amet',
                'last_name' => 'Lorem ipsum dolor sit amet',
                'email' => 'Lorem ipsum dolor sit amet',
                'password' => 'Lorem ipsum dolor sit amet',
                'nonce' => '2025-04-05 09:49:44',
                'nonce_expiry' => '2025-04-05 09:49:44',
                'created' => '2025-04-05 09:49:44',
                'modified' => '2025-04-05 09:49:44',
                'type' => 'Lorem ipsum dolor sit amet',
                'profile_picture' => 'Lorem ipsum dolor sit amet',
            ],
        ];
        parent::init();
    }
}
