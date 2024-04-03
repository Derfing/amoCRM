<?php

namespace App\Services;

use AmoCRM\Collections\TasksCollection;
use App\Exceptions\ValidateTaskDataException;
use App\Services\Prototypes\Factories\EntityFactory;

class AddTasksService
{
    /**
     * @throws ValidateTaskDataException
     */
    public function makeTasks($entityType, $baseEntityId): TasksCollection
    {
        return EntityFactory::createEntity($entityType, $baseEntityId)->makeTasks();
    }
}
