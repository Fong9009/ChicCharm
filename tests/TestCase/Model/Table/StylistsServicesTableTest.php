<?php
declare(strict_types=1);

namespace App\Test\TestCase\Model\Table;

use App\Model\Table\StylistsServicesTable;
use Cake\TestSuite\TestCase;

/**
 * App\Model\Table\StylistsServicesTable Test Case
 */
class StylistsServicesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \App\Model\Table\StylistsServicesTable
     */
    protected $StylistsServices;

    /**
     * Fixtures
     *
     * @var list<string>
     */
    protected array $fixtures = [
        'app.StylistsServices',
        'app.Stylists',
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
        $config = $this->getTableLocator()->exists('StylistsServices') ? [] : ['className' => StylistsServicesTable::class];
        $this->StylistsServices = $this->getTableLocator()->get('StylistsServices', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->StylistsServices);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \App\Model\Table\StylistsServicesTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \App\Model\Table\StylistsServicesTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
