<?php

namespace App\Services\Prototypes;

use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Helpers\EntityTypesInterface;
use Exception;
use Makeroi\Amocrm\Kernel\Auth\KAuth;

class Contact extends Entity
{
    private int $baseEntityId;

    public function __construct($id)
    {
        $this->baseEntityId = $id;
    }

    protected function getContact(): CompaniesCollection|ContactsCollection|null
    {
        try {
            return (new ContactsCollection())->add(KAuth::getApiClient()
                ->contacts()
                ->getOne($this->baseEntityId));
        } catch (Exception $e) {
            logger()->debug($e->getMessage());
            if ($e->getCode() == 429) abort(500, $e->getMessage());
            return null;
        }
    }

    protected function getAllLeads(): ?LeadsCollection
    {
        try {
            $contact = KAuth::getApiClient()->contacts()
                ->getOne($this->baseEntityId, [EntityTypesInterface::LEADS]);
            return $contact->getLeads();
        } catch (Exception $e) {
            logger()->debug($e->getMessage());
            if ($e->getCode() == 429) abort(500, $e->getMessage());
            return null;
        }
    }
}
