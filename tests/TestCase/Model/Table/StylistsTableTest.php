<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StylistsTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StylistsTable Test Case
 */
class StylistsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StylistsTable
     */
    protected $Stylists;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.Stylists',
        'app.Bookings',
        'app.Services',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Stylists') ? [] : ['className' => StylistsTable::class];
        $this->Stylists = $this->getTableLocator()->get('Stylists', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Stylists);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\StylistsTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\StylistsTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
