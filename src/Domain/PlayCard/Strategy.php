<?php

namespace App\Domain\PlayCard;

use App\Domain\Age;
use App\Domain\Card\CardType;
use App\Domain\Player;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\WonderPowerType;
use JetBrains\PhpStorm\Pure;

abstract class Strategy
{

    protected Age $age;
    /**
     * @var Player[]
     */
    protected array $players;
    /**
     * @var CardType[]
     */
    protected array $discard;

    /**
     * @param Age $age
     * @param Player[] $players
     * @param CardType[] $discard
     */
    public function __construct(Age $age, array $players, array $discard)
    {
        $this->age = $age;
        $this->players = $players;
        $this->discard = $discard;
    }

    abstract public function play(Player $player, string $cardName, string $action, ?string $tradeId): Strategy;

    public function neighboursOf(int $userId): Neighbourhood
    {
        $playerIds = array_map(fn(Player $player) => $player->id, $this->players);
        $lastIndex = count($playerIds) - 1;
        $index = array_search($userId, $playerIds);
        $prevIndex = $index === 0 ? $lastIndex : $index - 1;
        $nextIndex = $index === $lastIndex ? 0 : $index + 1;

        return new Neighbourhood($this->players[$nextIndex]->wonder, $this->players[$prevIndex]->wonder);
    }

    public function age(): Age
    {
        return $this->age;
    }

    public function players(): array
    {
        return $this->players;
    }

    public function discard(): array
    {
        return $this->discard;
    }

    /**
     * @param Player[] $committedPlayers
     * @return Player | null
     */
    #[Pure] protected function hasPowerToPlay(array $committedPlayers, WonderPowerType $powerType): ?Player
    {
        foreach ($committedPlayers as $player) {
            if ($player->wonder->hasPowerToPlay($powerType)) {
                return $player;
            }
        }
        return null;
    }
}
