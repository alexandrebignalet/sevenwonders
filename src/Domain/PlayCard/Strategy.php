<?php

namespace App\Domain\PlayCard;

use App\Domain\Age;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\Player;
use App\Domain\RotationDirectionFlow;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\Wonder;
use App\Domain\Wonder\WonderPowerType;
use JetBrains\PhpStorm\ArrayShape;
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
     * @param Player[] $players
     * @return bool
     */
    public function isAgeOver(array $players): bool
    {
        return array_reduce($players, function (bool $isAgeOver, Player $player) {
            return $isAgeOver && $player->hand->size() <= 1;
        }, true);
    }

    /**
     * @param Player[] $players
     * @param CardType[] $discardedCards
     * @return Strategy
     * @throws GameException
     */
    public function setupNextAge(array $players, array $discardedCards): Strategy
    {
        $age = $this->age->next();
        $warResolvedPlayers = $this->resolveWars($players);
        $nextAgeCardsHandedPlayers = array_map(fn(Player $player) => $player->receives($age->distributeHand()), $warResolvedPlayers);
        return new PlayCard($age, $nextAgeCardsHandedPlayers, array_merge($this->discard, $discardedCards));
    }

    /**
     * @param Player[] $players
     * @param CardType[] $discard
     * @return array{discard: array<CardType>, players: array<Player>}
     */
    #[ArrayShape(['discard' => "\App\Domain\Card\CardType[]", 'players' => "\App\Domain\Player[]"])]
    protected function discardEachPlayerLastCard(array $players, array $discard): array
    {
        return array_reduce($players, function ($acc, Player $player) {
            $result = $player->discardLastCard();
            if (isset($result['cardType'])) {
                $acc['discard'][] = $result['cardType'];
            }
            $acc['players'][] = $result['player'];
            return $acc;
        }, ['discard' => $discard, 'players' => []]);
    }

    /**
     * @param Player[] $committedPlayers
     * @param WonderPowerType $powerType
     * @return Player | null
     */
    #[Pure] protected function hasActionRequiredByWonderPower(array $committedPlayers, WonderPowerType $powerType): ?Player
    {
        foreach ($committedPlayers as $player) {
            if ($player->wonder->hasActionRequiredPower($powerType)) {
                return $player;
            }
        }
        return null;
    }

    /**
     * @param Player[] $players
     * @return Player[]
     */
    protected function resolveWars(array $players): array
    {
        return array_map(function (Player $player) {
            $neighbourhood = $this->neighboursOf($player->id);
            $leftBattle = $this->fight($player, $neighbourhood->left);
            $rightBattle = $this->fight($player, $neighbourhood->right);
            return $player->war($this->age, [$leftBattle, $rightBattle]);
        }, $players);
    }

    /**
     * @param Wonder $neighbour
     * @param Player $currentPlayer
     * @return WarResult
     */
    protected function fight(Player $currentPlayer, Wonder $neighbour): WarResult
    {
        $battleDiff = $currentPlayer->wonder->warPoints() - $neighbour->warPoints();

        switch (true) {
            case $battleDiff === 0:
                return WarResult::EX_AEQUO;
            case $battleDiff < 0:
                return WarResult::LOSE;
            case $battleDiff > 0:
                return WarResult::WON;
        }
    }

    /**
     * @param Player[] $players
     * @param CardType[] $discard
     * @return HalikarnassosPowerStrategy|null
     */
    #[Pure] protected function halikarnassosPowerOrNull(array $players, array $discard): ?HalikarnassosPowerStrategy
    {
        $haliPlayer = $this->hasActionRequiredByWonderPower($players, WonderPowerType::UNBURY_CARD);
        if (isset($haliPlayer) && count($discard) > 0) {
            return new HalikarnassosPowerStrategy($haliPlayer, $this->age, $players, $discard);
        }
        return null;
    }

    /**
     * @param Player[] $players
     * @return PlayCard
     */
    protected function setupNextRound(array $players): PlayCard
    {
        $rotatedHandPlayers = $this->rotatePlayersHand($players);
        return new PlayCard($this->age, $rotatedHandPlayers, $this->discard);
    }

    /**
     * @param Player[] $players
     * @return Player[]
     */
    private function rotatePlayersHand(array $players): array
    {
        $rotated = $players;
        $rotationDirectionFlow = $this->age->rotationDirectionFlow();
        $from = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? 0 : count($players) - 1;
        $to = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? count($players) : 0;
        $flow = $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? 1 : -1;

        for ($i = $from; $rotationDirectionFlow === RotationDirectionFlow::CLOCKWISE ? $i < $to : $i > $to; $i = $i + $flow) {

            $prevPlayerIndex = $i === $from ? $to === 0 ? $to : $to - 1 : $i - $flow;
            $prevPlayer = $players[$prevPlayerIndex];

            $currentPlayer = $players[$i];

            $rotated[$i] = $currentPlayer->receives($prevPlayer->hand->cards());
        }

        return $rotated;
    }
}
