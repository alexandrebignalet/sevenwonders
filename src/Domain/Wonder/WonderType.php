<?php

namespace App\Domain\Wonder;

enum WonderType {
    case RHODOS;
    case ALEXANDRIA;
    case GIZAH;
    case BABYLON;
    case OLYMPIA;
    case EPHESOS;
    case HALIKARNASSOS;

    public function wonder(): Wonder {
        return Wonder::initialize($this);
    }
}
