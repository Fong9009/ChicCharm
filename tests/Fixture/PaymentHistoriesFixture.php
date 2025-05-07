<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * PaymentHistoriesFixture
 */
class PaymentHistoriesFixture extends TestFixture
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
                'booking_id' => 1,
                'customer_id' => 1,
                'paypal_transaction_id' => 'Lorem ipsum dolor sit amet',
                'paypal_payer_id' => 'Lorem ipsum dolor sit amet',
                'payment_amount' => 1.5,
                'payment_currency' => 'L',
                'payment_status' => 'Lorem ipsum dolor sit amet',
                'payment_method' => 'Lorem ipsum dolor sit amet',
                'payment_date' => 1746609702,
                'notes' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
                'created_at' => 1746609702,
                'updated_at' => 1746609702,
            ],
        ];
        parent::init();
    }
}
