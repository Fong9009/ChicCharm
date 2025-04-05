<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BookingsStylistsFixture
 */
class BookingsStylistsFixture extends TestFixture
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
                'stylist_date' => '2025-04-05',
                'start_time' => '09:44:49',
                'end_time' => '09:44:49',
                'selected_cost' => 1.5,
                'booking_id' => 1,
                'stylist_id' => 1,
            ],
        ];
        parent::init();
    }
}
