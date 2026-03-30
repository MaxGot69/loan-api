<?php

namespace App\Enum;

enum LoanStatus: string {
    case APPROVED = 'approved';
    case REJECTED = 'rejected';

    /*public function isApproved(): bool{
        return $this->value === self::APPROVED;
    }
    */
}

