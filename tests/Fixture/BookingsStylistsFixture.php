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
                'start_time' => '07:00:22',
                'end_time' => '07:00:22',
                'selected_cost' => 1.5,
                'booking_id' => 1,
                'stylist_id' => 1,
            ],
        ];
        parent::init();
    }
}
