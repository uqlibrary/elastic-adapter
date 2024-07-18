<?php declare(strict_types=1);

namespace Elastic\Adapter\Tests\Unit\Search;

use Elastic\Adapter\Search\PointInTimeManager;
use Elastic\Client\ClientBuilderInterface;
use OpenSearch\Client;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Elastic\Adapter\Search\PointInTimeManager
 */
final class PointInTimeManagerTest extends TestCase
{
    private MockObject $client;
    private PointInTimeManager $pointInTimeManager;

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->client = $this->createMock(Client::class);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($this->client);

        $this->pointInTimeManager = new PointInTimeManager($clientBuilder);
    }

    public function test_point_in_time_can_be_opened(): void
    {
        $response = [
            'pit_id' => '46ToAwMDaWR5BXV1',
        ];

        $this->client
            ->expects($this->once())
            ->method('createPointInTime')
            ->with([
                'index' => 'test',
                'keep_alive' => '1m',
            ])
            ->willReturn($response);

        $this->assertSame('46ToAwMDaWR5BXV1', $this->pointInTimeManager->open('test', '1m'));
    }

    public function test_point_in_time_can_be_closed(): void
    {
        $this->client
            ->expects($this->once())
            ->method('deletePointInTime')
            ->with([
                'body' => [
                    'pit_id' => '46ToAwMDaWR5BXV1',
                ],
            ]);

        $this->assertSame($this->pointInTimeManager, $this->pointInTimeManager->close('46ToAwMDaWR5BXV1'));
    }

    /**
     * @noinspection ClassMockingCorrectnessInspection
     * @noinspection PhpUnitInvalidMockingEntityInspection
     */
    public function test_connection_can_be_changed(): void
    {
        $defaultClient = $this->createMock(Client::class);

        $defaultClient
            ->expects($this->never())
            ->method('deletePointInTime');

        $testClient = $this->createMock(Client::class);

        $testClient
            ->expects($this->once())
            ->method('deletePointInTime')
            ->with([
                'body' => [
                    'pit_id' => 'foo',
                ],
            ]);

        $clientBuilder = $this->createMock(ClientBuilderInterface::class);
        $clientBuilder->method('default')->willReturn($defaultClient);
        $clientBuilder->method('connection')->with('test')->willReturn($testClient);

        (new PointInTimeManager($clientBuilder))->connection('test')->close('foo');
    }
}
