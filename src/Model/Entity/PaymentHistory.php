<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * PaymentHistory Entity
 *
 * @property int $id
 * @property int|null $booking_id
 * @property int|null $customer_id
 * @property string $paypal_transaction_id
 * @property string|null $paypal_payer_id
 * @property string $payment_amount
 * @property string $payment_currency
 * @property string $payment_status
 * @property string|null $payment_method
 * @property \Cake\I18n\DateTime $payment_date
 * @property string|null $notes
 * @property \Cake\I18n\DateTime|null $created_at
 * @property \Cake\I18n\DateTime|null $updated_at
 *
 * @property \App\Model\Entity\Booking $booking
 * @property \App\Model\Entity\Customer $customer
 */
class PaymentHistory extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'booking_id' => true,
        'customer_id' => true,
        'paypal_transaction_id' => true,
        'paypal_payer_id' => true,
        'payment_amount' => true,
        'payment_currency' => true,
        'payment_status' => true,
        'payment_method' => true,
        'payment_date' => true,
        'notes' => true,
        'created_at' => true,
        'updated_at' => true,
        'booking' => true,
        'customer' => true,
    ];
}
