<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
class SubmitLoanRequest
{
    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Range(min : 0, max : 10000000)]
    public int $income;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Range(min: 100, max: 1000000)]
    public int $amount;

    #[Assert\NotBlank]
    #[Assert\Positive]
    #[Assert\Range(min: 1, max: 360)]
    public int $term;
}
