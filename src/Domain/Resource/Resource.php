<?php

namespace App\Domain\Resource;

use JetBrains\PhpStorm\Pure;

class Resource
{
    private int $clay;
    private int $stone;
    private int $ore;
    private int $wood;
    private int $papyrus;
    private int $glass;
    private int $cloth;

    /**
     * @param int $clay
     * @param int $stone
     * @param int $ore
     * @param int $wood
     * @param int $papyrus
     * @param int $glass
     * @param int $cloth
     */
    public function __construct(int $clay = 0, int $stone = 0, int $ore = 0, int $wood = 0, int $papyrus = 0, int $glass = 0, int $cloth = 0)
    {
        $this->clay = $clay;
        $this->stone = $stone;
        $this->ore = $ore;
        $this->wood = $wood;
        $this->papyrus = $papyrus;
        $this->glass = $glass;
        $this->cloth = $cloth;
    }

    /**
     * @return int|null
     */
    public function getClay(): int
    {
        return $this->clay;
    }

    /**
     * @return int|null
     */
    public function getStone(): int
    {
        return $this->stone;
    }

    /**
     * @return int|null
     */
    public function getOre(): int
    {
        return $this->ore;
    }

    /**
     * @return int|null
     */
    public function getWood(): int
    {
        return $this->wood;
    }

    /**
     * @return int|null
     */
    public function getPapyrus(): int
    {
        return $this->papyrus;
    }

    /**
     * @return int|null
     */
    public function getGlass(): int
    {
        return $this->glass;
    }

    /**
     * @return int|null
     */
    public function getCloth(): int
    {
        return $this->cloth;
    }

    #[Pure] public function plus(Resource $other): Resource
    {
        return new Resource(
            $this->clay + $other->clay,
            $this->stone + $other->stone,
            $this->ore + $other->ore,
            $this->wood + $other->wood,
            $this->papyrus + $other->papyrus,
            $this->glass + $other->glass,
            $this->cloth + $other->cloth
        );
    }

    #[Pure] public function minus(Resource $other): Resource
    {
        return new Resource(
            $this->clay - $other->clay,
            $this->stone - $other->stone,
            $this->ore - $other->ore,
            $this->wood - $other->wood,
            $this->papyrus - $other->papyrus,
            $this->glass - $other->glass,
            $this->cloth - $other->cloth
        );
    }

    public function isZeroOrNegative(): bool
    {
        return $this->clay <= 0 &&
            $this->stone <= 0 &&
            $this->ore <= 0 &&
            $this->wood <= 0 &&
            $this->papyrus <= 0 &&
            $this->glass <= 0 &&
            $this->cloth <= 0;
    }

    public function tradingCost(bool $hasComptoir = false, bool $hasMarket = false): int
    {
        $brownPrice = $hasComptoir ? 1 : 2;
        $greyPrice = $hasMarket ? 1 : 2;
        return $this->clay * $brownPrice +
            $this->stone * $brownPrice +
            $this->ore * $brownPrice +
            $this->wood * $brownPrice +
            $this->papyrus * $greyPrice +
            $this->glass * $greyPrice +
            $this->cloth * $greyPrice;
    }

    public function equals(Resource $other): bool
    {
        return $this->clay === $other->clay &&
            $this->stone === $other->stone &&
            $this->ore === $other->ore &&
            $this->wood === $other->wood &&
            $this->papyrus === $other->papyrus &&
            $this->glass === $other->glass &&
            $this->cloth === $other->cloth;
    }

    public function hasAtLeastOnePositive(): bool
    {
        return $this->clay > 0 ||
            $this->stone > 0 ||
            $this->ore > 0 ||
            $this->wood > 0 ||
            $this->papyrus > 0 ||
            $this->glass > 0 ||
            $this->cloth > 0;
    }

    /**
     * @param Resource $left
     * @param Resource $right
     * @return Share[]
     */
    public function availableShares(Resource $left, Resource $right): array
    {
        $this->shares = [];


        $this->foreachResourceValues($left, function (Resource $aLeftShare) use ($right) {
            $this->foreachResourceValues($right, function (Resource $aRightShare) use ($aLeftShare) {
                $sum = $aLeftShare->plus($aRightShare);
                if ($sum->equals($this)) {
                    $this->shares[] = new Share($aLeftShare, $aRightShare);
                }
            });
        });

        return $this->shares;
    }

    public function tradeId(): string
    {
        $clay = $this->clay > 0 ? "CLAY_{$this->clay}" : null;
        $stone = $this->stone > 0 ? "STONE_{$this->stone}" : null;
        $ore = $this->ore > 0 ? "ORE_{$this->ore}" : null;
        $wood = $this->wood > 0 ? "WOOD_{$this->wood}" : null;
        $glass = $this->glass > 0 ? "GLASS_{$this->glass}" : null;
        $cloth = $this->cloth > 0 ? "CLOTH_{$this->cloth}" : null;
        $papyrus = $this->papyrus > 0 ? "PAPYRUS_{$this->papyrus}" : null;
        $values = [$clay, $stone, $ore, $wood, $glass, $cloth, $papyrus];
        $filtered = array_filter($values, fn($v) => $v !== null);
        return array_reduce($filtered, fn($acc, $v) => "$acc$v", "");
    }

    #[Pure] public function focusOn(Resource $neededResource): Resource
    {
        return new Resource(
            $neededResource->clay > 0 ? $this->clay : 0,
            $neededResource->stone > 0 ? $this->stone : 0,
            $neededResource->ore > 0 ? $this->ore : 0,
            $neededResource->wood > 0 ? $this->wood : 0,
            $neededResource->papyrus > 0 ? $this->papyrus : 0,
            $neededResource->glass > 0 ? $this->glass : 0,
            $neededResource->cloth > 0 ? $this->cloth : 0,
        );
    }

    /**
     * @return void
     */
    private function foreachResourceValues(Resource $neighbourResource, callable $callback): void
    {
        for ($c = 0; $c <= $this->min($neighbourResource, 'clay'); $c++) {
            for ($s = 0; $s <= $this->min($neighbourResource, 'stone'); $s++) {
                for ($o = 0; $o <= $this->min($neighbourResource, 'ore'); $o++) {
                    for ($w = 0; $w <= $this->min($neighbourResource, 'wood'); $w++) {
                        for ($p = 0; $p <= $this->min($neighbourResource, 'papyrus'); $p++) {
                            for ($g = 0; $g <= $this->min($neighbourResource, 'glass'); $g++) {
                                for ($clo = 0; $clo <= $this->min($neighbourResource, 'cloth'); $clo++) {
                                    $callback(new Resource($c, $s, $o, $w, $p, $g, $clo));
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    private function min(Resource $other, string $propertyName)
    {
        return min($this->$propertyName, $other->$propertyName);
    }

    public function toString(): string
    {
        return "Resource(clay: {$this->clay}, stone: {$this->stone}, ore: {$this->ore}, wood: {$this->wood}, papyrus: {$this->papyrus}, glass: {$this->glass}, cloth: {$this->cloth})";
    }
}
