<?php

namespace App\Tests\Domain;

use App\Domain\Age;
use App\Domain\Card\Card;
use App\Domain\Card\CardType;
use App\Domain\Player;
use App\Domain\Wonder\Neighbourhood;
use App\Domain\Wonder\WonderFace;
use App\Domain\Wonder\WonderType;
use PHPUnit\Framework\TestCase;

class PlayerInitializationTest extends TestCase
{
    private Player $player;
    private int $userId = 1;
    private Age $age;
    private Neighbourhood $babylonNbh;

    public function setUp(): void
    {
        $this->age = Age::first(3);
        $rhodos = WonderType::RHODOS->wonder(WonderFace::DAY);
        $alexandria = WonderType::ALEXANDRIA->wonder(WonderFace::DAY);
        $babylon = WonderType::BABYLON->wonder(WonderFace::DAY);
        $rhodosNbh = new Neighbourhood($babylon, $alexandria);
        $alexandriaNbh = new Neighbourhood($rhodos, $babylon);
        $this->babylonNbh = new Neighbourhood($alexandria, $rhodos);

        $this->player = Player::initialize($this->userId, $this->age, $babylon);
    }

    public function test_should_have_3_coins()
    {
        $this->assertEquals(3, $this->player->wonder->coins);
    }

    public function test_should_have_a_wonder_picked_randomly()
    {
        $this->assertContains($this->player->wonder->type, WonderType::cases());
    }

    public function test_should_have_a_first_age_hand_with_more_than_7_cards()
    {
        $this->assertCount(7, $this->player->hand->cards());
        $firstAgeCardsName = array_map(fn(Card $card): CardType => $card->type, Age::first(3)->cards);
        foreach ($this->player->hand->availableActions($this->player->wonder, $this->babylonNbh) as $cardAction) {
            $this->assertContains($cardAction->cardType(), $firstAgeCardsName);
        }
    }

    public function test_should_have_no_selected_action()
    {
        $this->assertEquals(null, $this->player->selectedAction);
    }
}
