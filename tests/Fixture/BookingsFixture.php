<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BookingsFixture
 */
class BookingsFixture extends TestFixture
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
                'booking_date' => '2025-04-05 02:07:11',
                'total_cost' => 1.5,
                'remaining_cost' => 1.5,
                'customer_id' => 1,
            ],
        ];
        parent::init();
    }
}
