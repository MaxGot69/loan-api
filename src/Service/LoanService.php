<?php

namespace App\Service;

use App\Entity\LoanApplication;
use App\Repository\LoanApplicationRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\ScoringService;


//Сервис для обработки заявок
class LoanService
{
    private LoanApplicationRepository $loanApplicationRepository;
    private EntityManagerInterface $entityManager;

    private $scoringService; //Будет позже

    // Конструктор  Принимает зависимости через аргументы и Сохраняет их в свойства
    public function __construct( EntityManagerInterface $entityManager,
                                 ScoringService $scoringService,
                                 LoanApplicationRepository $loanApplicationRepository ){
        $this->entityManager = $entityManager;
        $this->scoringService = $scoringService;
        $this->loanApplicationRepository = $loanApplicationRepository;
    }

    public function processLoanApplication(array $data): LoanApplication {
        $loanApplication = new LoanApplication(); //Создает объект лоанАппликатион
        //Приниамю данныфе заявки
        $loanApplication->setIncome($data["income"]);
        $loanApplication->setAmount($data["amount"]);
        $loanApplication->setTerm($data["term"]);

        //Вызываю скорингСервис который сделаю позже
        //$this->scoringService->processLoanApplication($loanApplication, $data);
        $status = $this->scoringService->installStatus($data); //реализовать этот метод статуса в скорингСервисе
        $loanApplication->setStatus($status);

        //Доктрин к сохранению
        $this->entityManager->persist($loanApplication);
        //и сохр в БД
        $this->entityManager->flush();
        return $loanApplication; //и возвращаю созданную заявку
    }

}
