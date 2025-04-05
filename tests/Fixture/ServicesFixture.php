<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * ServicesFixture
 */
class ServicesFixture extends TestFixture
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
                'service_name' => 'Lorem ipsum dolor sit amet',
                'service_cost' => 1.5,
            ],
        ];
        parent::init();
    }
}
