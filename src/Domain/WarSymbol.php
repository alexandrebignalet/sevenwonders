<?php

namespace App\Domain;

use App\Domain\PlayCard\WarResult;

class WarSymbol
{
    public readonly int $ageId;
    public readonly WarResult $result;

    /**
     * @param int $ageNo
     * @param WarResult $result
     */
    public function __construct(int $ageNo, WarResult $result)
    {
        $this->ageId = $ageNo;
        $this->result = $result;
    }

    public function points()
    {
        return match ($this->result) {
            WarResult::WON => 1,
            WarResult::EX_AEQUO => 0,
            WarResult::LOSE => -1,
        };
    }
}
