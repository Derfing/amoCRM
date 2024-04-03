<?php

namespace App\Jobs;

use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Exceptions\AmoCRMMissedTokenException;
use AmoCRM\Exceptions\AmoCRMoAuthApiException;
use App\Exceptions\InvalidSettingsException;
use App\Exceptions\ValidateTaskDataException;
use App\Services\AddTasksService;
use Makeroi\Amocrm\Kernel\Auth\KAuth;
use \Makeroi\Amocrm\Jobs\BaseWebhookGlobalProcessJob;
class AddTaskWebhookGlobalProcessJob extends BaseWebhookGlobalProcessJob
{
    private int $entityType, $baseEntityId;

    public function __construct($data)
    {
        parent::__construct($data);
        $this->baseEntityId = head(data_get($this->data, '*.note.0.note.element_id', 0));
        $this->entityType = head(data_get($this->data, '*.note.0.note.element_type', 0));
    }

    /**
     * @throws AmoCRMApiException
     * @throws ValidateTaskDataException
     * @throws AmoCRMMissedTokenException
     * @throws AmoCRMoAuthApiException
     * @throws InvalidSettingsException
     */
    public function handle(): int
    {
        if (!KAuth::getSettingsBag()->isValid()) {
            logger()->debug('Ошибка в настройках', ['settings' => KAuth::getSettingsBag()->toArray()]);
            throw new InvalidSettingsException();
        }

        $service = new AddTasksService();

        KAuth::getApiClient()
            ->tasks()
            ->add($service->makeTasks($this->entityType, $this->baseEntityId));

        return true;
    }
}

