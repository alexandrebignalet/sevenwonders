<?php

namespace App\Domain;

use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;

class SevenWonders {


    private int $id;
    private Age $age;
    /**
     * @var Player[]
     */
    private array $players;


    #[Pure] static function start(int $roomId, array $userIds, bool $shuffle = true): SevenWonders {
        $wonders = WonderType::cases();
        if ($shuffle) shuffle($wonders);

        $age = Age::first(count($userIds), $shuffle);
        $players = array_map(fn(int $userId): Player => Player::initialize($userId, $age, array_pop($wonders)->wonder()), $userIds);
        self::setupNeighbourhood($players);
        return new SevenWonders($roomId, $age, $players);
    }

    /**
     * @param Player[] $players
     * @return void
     */
    public static function setupNeighbourhood(array $players): void
    {
        $playersCount = count($players);
        for ($i = 0; $i < $playersCount; $i++) {
            $leftNeighbour = $players[$i === 0 ? $playersCount - 1 : $i - 1];
            $rightNeighbour = $players[$i === $playersCount - 1 ? 0 : $i + 1];
            $players[$i]->setNeighbours([$leftNeighbour, $rightNeighbour]);
        }
    }

    /**
     * @throws GameException
     */
    public function playCard(int $userId, string $cardName, string $action)
    {
            $player = $this->findPlayer($userId);
            $player->play($cardName, $action);
    }

    public function __construct(int $id, Age $age, array $userIds)
    {
        $this->id = $id;
        $this->age = $age;
        $this->players = $userIds;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function age(): Age
    {
        return $this->age;
    }

    public function players(): array
    {
        return $this->players;
    }

    /**
     * @throws GameException
     */
    private function findPlayer(int $userId): Player
    {
        foreach ($this->players as $player) {
            if ($player->id() === $userId) {
                return $player;
            }
        }
        throw GameExceptionType::PLAYER_NOT_FOUND->exception();
    }


}
