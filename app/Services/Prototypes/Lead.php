<?php

namespace App\Services\Prototypes;

use AmoCRM\Collections\CompaniesCollection;
use AmoCRM\Collections\ContactsCollection;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Helpers\EntityTypesInterface;
use Exception;
use Makeroi\Amocrm\Kernel\Auth\KAuth;

class Lead extends Entity
{
    private int $baseEntityId;

    public function __construct($id)
    {
        $this->baseEntityId = $id;
    }

    protected function getContact(): CompaniesCollection|ContactsCollection|null
    {
        try {
            $leads = KAuth::getApiClient()
                ->leads()->getOne($this->baseEntityId, [EntityTypesInterface::CONTACTS]);
            return (new ContactsCollection())->add($leads->getMainContact());
        } catch (Exception $e) {
            logger()->debug($e->getMessage());
            if ($e->getCode() == 429) abort(500, $e->getMessage());
            return null;
        }
    }

    protected function getAllLeads(): ?LeadsCollection
    {
        try {
            $contact = KAuth::getApiClient()->leads()
                ->getOne($this->baseEntityId, [EntityTypesInterface::CONTACTS])
                ->getMainContact(); //null? 429, no content
            return KAuth::getApiClient()
                ->contacts()
                ->getOne($contact->getId(), [EntityTypesInterface::LEADS])
                ->getLeads();
        } catch (Exception $e) {
            logger()->debug($e->getMessage());
            if ($e->getCode() == 429) abort(500, $e->getMessage());
            return null;
        }
    }
}
