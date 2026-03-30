<?php

namespace App\Controller;

use App\DTO\SubmitLoanRequest;
use App\Entity\LoanApplication;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\LoanService;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Repository\LoanApplicationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/*
 * TODO: упростить контроллер часть логики вынести, дто вместо дата, ручные запросы джсон убрать
 */

final class LoanController extends AbstractController
{
    private LoanService $loanService;

    public function __construct(LoanService $loanService, LoanApplicationRepository $loanApplicationRepository){
        $this->loanService = $loanService;
        $this->loanApplicationRepository = $loanApplicationRepository;
    }
    #[Route('/loan', name: 'app_loan', methods: ['POST'])]
    public function createApplication(#[MapRequestPayload] SubmitLoanRequest $submitLoanRequest): JsonResponse
    {
       //получить данные JSON и извлечь income, amount, term и декодир в массив
        $data = [
            'income' =>$submitLoanRequest->income,
            'amount' =>$submitLoanRequest->amount,
            'term' =>$submitLoanRequest->term,
        ];
         //Вызвать `LoanService`** : Передать данные в сервис и Получить созданную заявку

        $loanApplication = $this->loanService->processLoanApplication($data);

        // Вернуть ответ: Статус HTTP 201 (Created) , Тело ответа — данные заявки (id, статус, сумма, срок, доход)
        return $this->json([
            'id' => $loanApplication->getId(),
            'income' => $loanApplication->getIncome(),
            'amount' => $loanApplication->getAmount(),
            'term' => $loanApplication->getTerm(),
            'status' => $loanApplication->getStatus(),
        ],201);

    }
    #[Route('/loan/{id}', name: 'app_loan_show', methods: ['GET'])]
    public function getApplication(int $id): JsonResponse {

        $loanID = $this->loanApplicationRepository->find($id);
        //Если не найден то 404
        if(!$loanID) {
            throw $this->createNotFoundException('Loan application not found');
        }
        return $this->json([
            'id' => $loanID->getId(),
            'income' => $loanID->getIncome(),
            'amount' => $loanID->getAmount(),
            'term' => $loanID->getTerm(),
            'status' => $loanID->getStatus(),
        ],200);

    }

    #[Route('/loans', name: 'loan_list', methods: ['GET'])]
    public function listLoans(Request $request): JsonResponse {
        //Получить параметры пагинации из запроса
        $page = $request->query->getInt("page", 1);
        $limit = $request->query->getInt("limit", 10);

        //ВЦычисляем offset
        $offset = ($page - 1) * $limit;

        //Вызываю метод из репозитория
        $listApplication = $this->loanApplicationRepository->findAllWithPagination($offset, $limit);

        //Тут прохожусь по каждой заявке, собираю массив с полями и кладу в data
        $data = [];
        foreach ($listApplication as $loan) {
            $data[] = [
                'id' => $loan->getId(),
                'income' => $loan->getIncome(),
                'amount' => $loan->getAmount(),
                'term' => $loan->getTerm(),
                'status' => $loan->getStatus(),
            ];
        }
        return $this->json($data);
    }
}
