<?php

namespace App\Services\Prototypes;

use AmoCRM\Collections\BaseApiCollection;
use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\TasksCollection;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use AmoCRM\Filters\LeadsFilter;
use AmoCRM\Models\TaskModel;
use App\Exceptions\ValidateTaskDataException;
use Exception;
use Makeroi\Amocrm\Kernel\Auth\KAuth;

abstract class Entity
{
    /**
     * @throws ValidateTaskDataException
     */
    public function makeTasks(): TasksCollection
    {
        try {
            $entities = $this->getEntities();
        } catch (Exception $e) {
            logger()->notice('Невалидные данные запроса');
            throw new ValidateTaskDataException('Невалидные данные запроса');
        }

        if (!$entities) {
            logger()->notice('Нет подходящих сущностей');
            throw new ValidateTaskDataException('Нет подходящих сущностей');
        }

        $tasks = new TasksCollection();
        $settings = KAuth::getSettingsBag();

        foreach ($entities as $entity) {
            $tasks->add((new TaskModel)
                ->setEntityId($entity->getId())
                ->setText($settings->getText())
                ->setResponsibleUserId($entity->getResponsibleUserId())
                ->setCompleteTill($settings->getTaskTill())
                ->setEntityType($entity->getType())
                ->setTaskTypeId($settings->getTaskType()));
        }

        return $tasks;
    }

    protected function getEntities(): ?BaseApiCollection
    {
        return match (KAuth::getSettingsBag()->getType()) {
            'contact' => $this->getContact(),
            'all_leads' => $this->getAllLeads(),
            'active_lead' => $this->getActiveLeads(),
            default => null
        };
    }

    abstract protected function getContact(): CompaniesCollection|ContactsCollection|null;

    abstract protected function getAllLeads(): ?LeadsCollection;

    /**
     * @throws AmoCRMoAuthApiException
     * @throws AmoCRMApiException
     * @throws AmoCRMMissedTokenException
     */
    protected function getActiveLeads(): ?LeadsCollection
    {
        $leads = $this->getAllLeads();

        if (empty($leads)) return null;

        $syncedLeads = new LeadsCollection();

        foreach ($leads->chunk(50) as $chunk) {
            $leads = KAuth::getApiClient()->leads()
                ->get((new LeadsFilter)
                    ->setIds($chunk->pluck('id')));
            $syncedLeads = $syncedLeads->merge($leads);
        }

        $activeLeads = new LeadsCollection();
        foreach ($leads as $lead) {
            if (!in_array($lead->getStatusId(), [142, 143])) {
                $activeLeads->add($lead);
            }
        }
        return $activeLeads;
    }
}
