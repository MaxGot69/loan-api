<?php

namespace App\DataFixtures;

use App\Entity\LoanApplication;
use App\Enum\LoanStatus;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $applications = [
            ['income' => 5000, 'amount' => 2000, 'term' => 12, 'status' => LoanStatus::APPROVED],
            ['income' => 1000, 'amount' => 2000, 'term' => 12, 'status' => LoanStatus::REJECTED],
            ['income' => 8000, 'amount' => 3000, 'term' => 24, 'status' => LoanStatus::APPROVED],
            ['income' => 2000, 'amount' => 1500, 'term' => 6, 'status' => LoanStatus::REJECTED],
            ['income' => 10000, 'amount' => 4000, 'term' => 36, 'status' => LoanStatus::APPROVED],
            ['income' => 3000, 'amount' => 2000, 'term' => 12, 'status' => LoanStatus::REJECTED],
            ['income' => 7000, 'amount' => 2500, 'term' => 18, 'status' => LoanStatus::APPROVED],
            ['income' => 1500, 'amount' => 1000, 'term' => 6, 'status' => LoanStatus::REJECTED],
            ['income' => 12000, 'amount' => 5000, 'term' => 48, 'status' => LoanStatus::APPROVED],
            ['income' => 4000, 'amount' => 3000, 'term' => 12, 'status' => LoanStatus::REJECTED],
        ];

        foreach ($applications as $data) {
            $loan = new LoanApplication();
            $loan->setIncome($data['income']);
            $loan->setAmount($data['amount']);
            $loan->setTerm($data['term']);
            $loan->setStatus($data['status']);
            $manager->persist($loan);
        }

        $manager->flush();
    }
}
