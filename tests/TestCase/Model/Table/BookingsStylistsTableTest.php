<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\BookingsStylistsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\BookingsStylistsTable Test Case
 */
class BookingsStylistsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\BookingsStylistsTable
     */
    protected $BookingsStylists;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.BookingsStylists',
        'app.Bookings',
        'app.Stylists',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('BookingsStylists') ? [] : ['className' => BookingsStylistsTable::class];
        $this->BookingsStylists = $this->getTableLocator()->get('BookingsStylists', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->BookingsStylists);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\BookingsStylistsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\BookingsStylistsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
