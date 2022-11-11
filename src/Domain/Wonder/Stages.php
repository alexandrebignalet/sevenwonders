<?php

namespace App\Domain\Wonder;

use JetBrains\PhpStorm\Pure;

class Stages
{

    /**
     * @var Stage[]
     */
    public readonly array $values;

    /**
     * @param Stage[] $stages
     */
    public function __construct(Stage ...$stages)
    {
        $this->values = $stages;
    }

    public function build(): Stages
    {
        return new Stages(
            ...array_map(fn(Stage $stage) => $stage === $this->stageToBuild() ? $stage->build() : $stage, $this->values)
        );
    }

    #[Pure] public function stageToBuild(): ?Stage
    {
        foreach ($this->values as $stage) {
            if (!$stage->isBuilt()) {
                return $stage;
            }
        }
    }

    #[Pure] public function powerRequiresAction(): bool|WonderPowerType
    {
        foreach ($this->values as $stage) {
            if ($stage->powerRequiresAction() !== false) {
                return $stage->powerRequiresAction();
            }
        }
        return false;
    }
}
