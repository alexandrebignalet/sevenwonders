<?php

namespace App\Domain;

use App\Domain\PlayCard\PlayCardStrategy;
use App\Domain\PlayCard\Strategy;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use JetBrains\PhpStorm\Pure;

class SevenWonders
{
    public readonly int $id;

    public readonly Strategy $state;


    #[Pure] static function start(int $roomId, array $userIds, bool $shuffle = true, ?array $availableWonderTypes = null, ?array $availableWonderFaces = null): SevenWonders
    {
        $playersCount = count($userIds);
        $wonders = self::setupWonders($playersCount, $shuffle, $availableWonderTypes, $availableWonderFaces);
        $age = Age::first($playersCount, $shuffle);

        $userWonders = [];
        for ($i = 0; $i < $playersCount; $i++) {
            $userWonders[] = [$userIds[$i], $wonders[$i]];
        }

        $players = array_map(function (array $userWonder) use ($age): Player {
            $userId = $userWonder[0];
            $wonder = $userWonder[1];

            return Player::initialize($userId, $age, $wonder);
        }, $userWonders);

        return new SevenWonders($roomId, new PlayCardStrategy($age, $players, []));
    }

    /**
     * @param bool $shuffle
     * @return Wonder[]
     */
    public static function setupWonders(int $playersCount, bool $shuffle, ?array $availableWonderTypes = null, ?array $availableWonderFaces = null): array
    {
        $wonderTypes = $availableWonderTypes !== null ? $availableWonderTypes : WonderType::cases();
        $wonderFaces = $availableWonderFaces !== null ? $availableWonderFaces : WonderFace::cases();
        if ($shuffle) {
            shuffle($wonderTypes);
            shuffle($wonderFaces);
        }
        $gameWondersType = array_chunk($wonderTypes, $playersCount)[0];

        return array_map(fn(WonderType $type): Wonder => $type->wonder($wonderFaces[0]), $gameWondersType);
    }

    /**
     * @param int $userId
     * @param string $cardName
     * @param string $action
     * @param string|null $tradeId
     * @return SevenWonders
     * @throws GameException
     */
    public function playCard(int $userId, string $cardName, string $action, ?string $tradeId = null): SevenWonders
    {
        $player = $this->findPlayer($userId);
        $state = $this->state->play($player, $cardName, $action, $tradeId);
        return new SevenWonders($this->id, $state);
    }

    /**
     * @param int $id
     * @param Strategy $state
     */
    public function __construct(int $id, Strategy $state)
    {
        $this->id = $id;
        $this->state = $state;
    }

    public function age(): Age
    {
        return $this->state->age();
    }

    public function players(): array
    {
        return $this->state->players();
    }

    public function discard(): array
    {
        return $this->state->discard();
    }

    /**
     * @throws GameException
     */
    public function findPlayer(int $userId): Player
    {
        foreach ($this->players() as $player) {
            if ($player->id === $userId) {
                return $player;
            }
        }

        throw GameExceptionType::PLAYER_NOT_FOUND->exception();
    }
}
