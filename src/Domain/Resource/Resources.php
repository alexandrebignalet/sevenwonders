<?php

namespace App\Domain\Resource;

use JetBrains\PhpStorm\Pure;

class Resources
{

    /**
     * @var Resource[]
     */
    private array $resources;

    #[Pure] public static function single(int $clay = 0, int $stone = 0, int $ore = 0, int $wood = 0, int $papyrus = 0, int $glass = 0, int $cloth = 0): Resources
    {
        return new Resources(new Resource($clay, $stone, $ore, $wood, $papyrus, $glass, $cloth));
    }

    #[Pure] public static function empty(): Resources
    {
        return new Resources();
    }

    public function __construct(Resource ...$resources)
    {
        $this->resources = $resources;
    }

    public function plus(Resources $resourcesToAdd): Resources
    {
        $actualResources = $this->resources;

        if ($this->count() === 0) {
            return $resourcesToAdd;
        }

        $newResources = array_reduce($actualResources, function (array $acc, Resource $actualResource) use ($resourcesToAdd): array {

            if ($resourcesToAdd->count() === 0) {
                $acc[] = $actualResource;
                return $acc;
            }

            $updates = array_reduce($resourcesToAdd->resources, function (array $inner, Resource $resourceToAdd) use ($actualResource) {
                $inner[] = $actualResource->plus($resourceToAdd);
                return $inner;
            }, []);

            return array_merge($acc, $updates);
        }, []);

        return new Resources(...$newResources);
    }

    private function minus(Resource $other): Resources
    {
        $result = array_reduce($this->resources, function (array $acc, Resource $resource) use ($other) {
            $acc[] = $other->minus($resource->focusOn($other));
            return $acc;
        }, []);

        return new Resources(...$result);
    }

    /**
     * @return Resource[]
     */
    public function resources(): array
    {
        return $this->resources;
    }

    public function missingToEqual(Resource $resourceCost): Resources
    {
        if ($this->count() === 0) {
            return $resourceCost->isZeroOrNegative() ? Resources::empty() : new Resources($resourceCost);
        }

        $results = $this->minus($resourceCost);

        $enoughResources = $results->findFirstZeroOrNegative() !== null;
        if ($enoughResources) {
            return Resources::empty();
        }


        return $results;
    }

    #[Pure] private function findFirstZeroOrNegative(): ?Resource
    {
        foreach ($this->resources as $resource) {
            if ($resource->isZeroOrNegative()) {
                return $resource;
            }
        }
        return null;
    }

    public function count(): int
    {
        return count($this->resources);
    }

    public function toString(): string
    {
        return implode(" OR ", array_map(fn(Resource $resource) => $resource->toString(), $this->resources));
    }
}
