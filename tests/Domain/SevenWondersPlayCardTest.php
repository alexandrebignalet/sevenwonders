<?php

namespace App\Tests\Domain;

use App\Domain\Age;
use App\Domain\Card\CardType;
use App\Domain\GameException;
use App\Domain\GameExceptionType;
use App\Domain\SevenWonders;
use PHPUnit\Framework\TestCase;

class SevenWondersPlayCardTest extends TestCase
{
    private SevenWonders $game;
    private array $userIds = [1, 2, 3];

    public function setUp(): void
    {
        $this->game = SevenWonders::start(1, $this->userIds, false);
    }

    public function test_should_not_allow_an_unknown_player_to_play() {
        $this->expectExceptionMessage(GameExceptionType::PLAYER_NOT_FOUND->exception()->getMessage());

        $this->game->playCard(10, 'Panthéon', 'build_structure');
    }

    public function test_should_not_allow_to_play_a_card_not_in_player_hand() {
        $this->expectExceptionMessage(GameExceptionType::CARD_PLAY_NOT_ALLOWED->exception()->getMessage());

        $this->game->playCard(1, 'Panthéon', 'build_structure');
    }

    public function test_should_not_allow_unknown_action() {
        $this->expectExceptionMessage(GameExceptionType::UNKNOWN_ACTION->exception()->getMessage());

        $cardName = CardType::CARRIERE->value;
        $this->game->playCard(1, $cardName, 'dance');
    }
}
