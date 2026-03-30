<?php

namespace App\Service;

use App\Enum\LoanStatus;
use Symfony\Contracts\Cache\CacheInterface;

class ScoringService
{

    private CacheInterface $cache;
    public function __construct(CacheInterface $cache) {
        $this->cache = $cache;
    }
    /*
     * логика скоринга: income > amount * 2 → approve
     */
    public function installStatus(array $data) {
        //Приниамю данныфе заявки
        $income = $data["income"];
        $amount = $data["amount"];
        $term = $data["term"];

        // ключ для кеша
        $cacheKey = sprintf('scoring_%d_%d_%d', $income, $amount, $term);

        // Пытаемся получить результат из кеша
        return $this->cache->get($cacheKey, function () use ($income, $amount) {
            // Если в кеше нет — считаем статус
        if ($income > $amount * 2) {
            return LoanStatus::APPROVED;
        }
            return LoanStatus::REJECTED;
        });
    }
}
