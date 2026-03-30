<?php

namespace App\Tests\Service;

use App\Entity\LoanApplication;
use App\Enum\LoanStatus;
use App\Service\LoanService;
use App\Service\ScoringService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use App\Repository\LoanApplicationRepository;

class LoanServiceTest extends TestCase
{
    public function testProcessLoanApplicationPersistsEntityWithCorrectData(): void
    {
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $scoringService = $this->createMock(ScoringService::class);
        $repository = $this->createMock(LoanApplicationRepository::class);

        $data = [
            'income' => 5000,
            'amount' => 1500,
            'term'   => 24,
        ];

        $scoringService->expects($this->once())
            ->method('installStatus')
            ->with($this->equalTo($data))
            ->willReturn(LoanStatus::APPROVED);

        $entityManager->expects($this->once())
            ->method('persist')
            ->with($this->callback(function ($entity) use ($data) {
                if (!$entity instanceof LoanApplication) {
                    return false;
                }

                return $entity->getIncome() === $data['income']
                    && $entity->getAmount() === $data['amount']
                    && $entity->getTerm() === $data['term']
                    && $entity->getStatus() === LoanStatus::APPROVED;
            }));

        $entityManager->expects($this->once())
            ->method('flush');

        $service = new LoanService($entityManager, $scoringService, $repository);

        $result = $service->processLoanApplication($data);

        $this->assertInstanceOf(LoanApplication::class, $result);
        $this->assertSame($data['income'], $result->getIncome());
        $this->assertSame($data['amount'], $result->getAmount());
        $this->assertSame($data['term'], $result->getTerm());
        $this->assertSame(LoanStatus::APPROVED, $result->getStatus());
    }
}

