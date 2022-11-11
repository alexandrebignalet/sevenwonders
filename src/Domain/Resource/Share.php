<?php

namespace App\Domain\Resource;

class Share
{
    /**
     * @var Resource
     */
    private $left;
    /**
     * @var Resource
     */
    private $right;

    public function __construct(Resource $left, Resource $right)
    {
        $this->left = $left;
        $this->right = $right;
    }

    public function getLeft(): Resource
    {
        return $this->left;
    }

    public function getRight(): Resource
    {
        return $this->right;
    }


    public function equals(Share $other): bool
    {
        return $this->right->equals($other->right) && $this->left->equals($other->left);
    }
}
