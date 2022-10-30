<?php

namespace App\Tests\Domain;

use App\Domain\Age;
use App\Domain\Card\Card;
use App\Domain\Card\CardType;
use App\Domain\Player;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class PlayerInitializationTest extends TestCase
{
    private Player $player;
    private int $userId = 1;
    private Age $age;
    private Player $aPlayer;
    private Player $anotherPlayer;

    public function setUp(): void
    {
        $this->age = Age::first(3);
        $this->aPlayer = Player::initialize($this->userId + 1, $this->age, WonderType::RHODOS->wonder());
        $this->anotherPlayer = Player::initialize($this->userId + 2, $this->age, WonderType::ALEXANDRIA->wonder());
        $this->player = Player::initialize($this->userId, $this->age, WonderType::BABYLON->wonder());
        $this->player->setNeighbours([$this->aPlayer, $this->anotherPlayer]);
        $this->aPlayer->setNeighbours([$this->player, $this->anotherPlayer]);
        $this->anotherPlayer->setNeighbours([$this->aPlayer, $this->player]);
    }

    public function test_should_have_3_coins() {
        $this->assertEquals(3, $this->player->coins());
    }

    public function test_should_have_a_wonder_picked_randomly() {
        $this->assertContains($this->player->wonder()->type(), WonderType::cases());
    }

    public function test_should_have_a_first_age_hand_with_7_cards() {
        $this->assertEquals(7, $this->player->hand()->size());
        $firstAgeCardsName = array_map(fn(Card $card): CardType => $card->type(), Age::first(3)->cards());
        foreach ($this->player->hand() as $card) {
            $this->assertContains($card->type(), $firstAgeCardsName);
        }
    }

    public function test_should_have_no_selected_action() {
        $this->assertEquals(null, $this->player->selectedAction());
    }

    public function test_should_have_two_neighbours() {
        $this->assertCount(2, $this->player->neighbours());
    }
}
