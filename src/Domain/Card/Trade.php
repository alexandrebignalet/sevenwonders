<?php

namespace App\Domain\Card;

class Trade
{
    private Offer $leftNeighbourOffer;
    private Offer $rightNeighbourOffer;

    public function __construct(Offer $leftNeighbourOffer, Offer $rightNeighbourOffer)
    {
        $this->leftNeighbourOffer = $leftNeighbourOffer;
        $this->rightNeighbourOffer = $rightNeighbourOffer;
    }

    /**
     * @return Offer
     */
    public function getLeftNeighbourOffer(): Offer
    {
        return $this->leftNeighbourOffer;
    }

    /**
     * @return Offer
     */
    public function getRightNeighbourOffer(): Offer
    {
        return $this->rightNeighbourOffer;
    }

    public function id(): string
    {
        return "{$this->leftNeighbourOffer->tradeId()}/{$this->rightNeighbourOffer->tradeId()}";
    }

    public function toString()
    {
        return <<<EOD
            Trade {
                left = {$this->leftNeighbourOffer->toString()}
                right = {$this->rightNeighbourOffer->toString()}
            }
        EOD;

    }


}
