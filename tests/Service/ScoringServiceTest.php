<?php

namespace App\Tests\Service;

use App\Enum\LoanStatus;
use App\Service\ScoringService;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\Cache\CacheInterface;

class ScoringServiceTest extends TestCase
{
    public function testInstallStatusApprovesWhenIncomeMoreThanDoubleAmount(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with(
                $this->callback(static fn ($key): bool => is_string($key)),
                $this->callback(static fn ($callback): bool => is_callable($callback))
            )
            ->willReturnCallback(function (string $key, callable $callback) {
                return $callback();
            });

        $service = new ScoringService($cache);

        $status = $service->installStatus([
            'income' => 3000,
            'amount' => 1000,
            'term'   => 12,
        ]);

        $this->assertInstanceOf(LoanStatus::class, $status);
        $this->assertSame(LoanStatus::APPROVED, $status);
    }

    public function testInstallStatusRejectsWhenIncomeNotEnough(): void
    {
        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with(
                $this->callback(static fn ($key): bool => is_string($key)),
                $this->callback(static fn ($callback): bool => is_callable($callback))
            )
            ->willReturnCallback(function (string $key, callable $callback) {
                return $callback();
            });

        $service = new ScoringService($cache);

        $status = $service->installStatus([
            'income' => 1500,
            'amount' => 1000,
            'term'   => 12,
        ]);

        $this->assertInstanceOf(LoanStatus::class, $status);
        $this->assertSame(LoanStatus::REJECTED, $status);
    }

    public function testInstallStatusUsesCacheKeyBasedOnData(): void
    {
        $expectedKey = sprintf('scoring_%d_%d_%d', 2000, 800, 6);

        $cache = $this->createMock(CacheInterface::class);

        $cache->expects($this->once())
            ->method('get')
            ->with(
                $this->equalTo($expectedKey),
                $this->callback(static fn ($callback): bool => is_callable($callback))
            )
            ->willReturn(LoanStatus::APPROVED);

        $service = new ScoringService($cache);

        $status = $service->installStatus([
            'income' => 2000,
            'amount' => 800,
            'term'   => 6,
        ]);

        $this->assertSame(LoanStatus::APPROVED, $status);
    }
}

