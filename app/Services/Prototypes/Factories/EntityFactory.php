<?php

namespace App\Services\Prototypes\Factories;

use App\Services\Prototypes\Entity;
use App\Services\Prototypes\Contact;
use App\Services\Prototypes\Lead;
use App\Services\Prototypes\Company;

class EntityFactory {
    static function createEntity(int $entityType, int $baseEntityId): Entity {
        return match ($entityType) {
            1 => new Contact($baseEntityId),
            2 => new Lead($baseEntityId),
            3 => new Company($baseEntityId),
            default => throw new \InvalidArgumentException('Invalid entity type'),
        };
    }
}
