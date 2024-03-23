<?php

namespace App\Services;

use AmoCRM\Models\LeadModel;
use Illuminate\Http\Request;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use AmoCRM\Client\LongLivedAccessToken;
use AmoCRM\EntitiesServices\CustomFields;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\ContactsCollection;

use function PHPUnit\Framework\fileExists;
use function PHPUnit\Framework\isInstanceOf;
use AmoCRM\Collections\Leads\LeadsCollection;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\NumericCustomFieldValuesModel;

use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\NumericCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NumericCustomFieldValueCollection;
use Dflydev\DotAccessData\Data;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class AmoCRMService
{
    private $apiClient;

    public function __construct()
    {
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient();

        $longLivedAccessToken = new LongLivedAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJiYjE1MTJiYzk5OTQ2NmRjOTFhYzlhNGQwYTcwNzI3ZmI3OTFjMjQzMmRlODcyY2M2NzQ4YWIyN2MwZTBiNjRiMTZiMzBjM2I1M2MzNDJjIn0.eyJhdWQiOiJiODdkMjBlNS01N2FjLTQyNGQtYTZhOC02YTlkNWFmNmI1YzQiLCJqdGkiOiIyYmIxNTEyYmM5OTk0NjZkYzkxYWM5YTRkMGE3MDcyN2ZiNzkxYzI0MzJkZTg3MmNjNjc0OGFiMjdjMGUwYjY0YjE2YjMwYzNiNTNjMzQyYyIsImlhdCI6MTcxMTA0NDkxMCwibmJmIjoxNzExMDQ0OTEwLCJleHAiOjE3NDI1MTUyMDAsInN1YiI6IjEwODM0NDE0IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxNjQ4Nzk4LCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiNDA1NzM3MmQtNTQxOC00NzFhLThiYzYtOTQyYTQzNmRkMmRmIn0.R4G-yD9M0QYRMye-2ifMIXrCJ0JVCMo3s59pLMcdGKcqe-5FlNCllCWFJdB7AuSdcUvTbw5zSvN_vtt0WwVdmncDHUpXym9V1aPbEXnwPL8PZLt-PY3_-ATlypVejE4cBhv0AAO89skUOcB3FLJrpo8PrShHJabHMbhWDrHzalFAeCrWPJJLO14suTUb0BjSnaMI2eIp8wQq_M7h8Wi5XBQUdSog8Cdksvr9sSWHvEztgSMtvK_wbSH5b8kK7Lp8_QxatGT3sqwplBFVv8r3dTGjJe7JABeDXj53B9RwF_CilW4M9jkt4daIxbt2LSW9QYtAVPLAomX1Dfz9ixAjfA');

        $apiClient->setAccessToken($longLivedAccessToken)->setAccountBaseDomain('derendaevkosta45.amocrm.ru');

        $this->apiClient = $apiClient;
    }

    public function createLead($data)
    {
        $leadsService = $this->apiClient->leads();
        $lead = new LeadModel();


        $lead->setName($data['transaction_name'])
            ->setPrice($data['budget']);

        $leadCustomFields = new CustomFieldsValuesCollection();

        $cost = new NumericCustomFieldValuesModel();
        $cost->setFieldId(1505841);
        $cost->setValues((new NumericCustomFieldValueCollection)
            ->add((new NumericCustomFieldValueModel)->setValue($data['cost'])));
        $leadCustomFields->add($cost);

        $profit = new NumericCustomFieldValuesModel();
        $profit->setFieldId(1505843);
        $profit->setValues((new NumericCustomFieldValueCollection)
            ->add((new NumericCustomFieldValueModel)->setValue($data['budget'] - $data['cost'])));
        $leadCustomFields->add($profit);

        $lead->setCustomFieldsValues($leadCustomFields);

        try {
            $lead = $leadsService->addOne($lead);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }
    }

    private function getProfit($newProfit)
    {
        $profit = new NumericCustomFieldValuesModel();
        $profit->setFieldId(1505843);
        $profit->setValues((new NumericCustomFieldValueCollection)
            ->add((new NumericCustomFieldValueModel)->setValue($newProfit)));

        return $profit;
    }

    private function getCost($newCost)
    {
        $cost = new NumericCustomFieldValuesModel();
        $cost->setFieldId(1505841);
        $cost->setValues((new NumericCustomFieldValueCollection)
            ->add((new NumericCustomFieldValueModel)->setValue($newCost)));

        return $cost;
    }

    public function editLead($data)
    {
        $leadsService = $this->apiClient->leads();
        $lead = $leadsService->getOne($data['transaction_id']);

        $lead->setName($data['transaction_name'] ? $data['transaction_name'] : $lead->getName())
            ->setPrice($data['budget'] ? $data['budget'] : $lead->getPrice());

        $leadCustomFields = $lead->getCustomFieldsValues();

        if ($data['cost'] && $data['budget']) {
            $leadCustomFields->add($this->getCost($data['cost']));
            $leadCustomFields->add($this->getProfit($data['cost'] - $data['budget']));
        } elseif ($data['cost']) {
            $leadCustomFields->add($this->getCost($data['cost']));
            $leadCustomFields->add($this->getProfit($lead->getPrice() - $data['cost']));
        } elseif ($data['budget']) {
            $lead->setPrice($data['budget']);
            $leadCustomFields->add($this->getProfit($lead->getPrice() - $lead->getCustomFieldsValues()
                ->getBy('fieldId', 1505841)
                ->getValues()
                ->first()
                ->getValue()));
        }

        $lead->setCustomFieldsValues($leadCustomFields);

        try {
            $lead = $leadsService->updateOne($lead);
        } catch (AmoCRMApiException $e) {
            printError($e);
            die;
        }
    }

    private function generateLead()
    {
        $lead = array();

        $lead['transaction_name'] = random_int(0, 2) ? fake('ru')->words(2, true) : '';
        $lead['cost'] = random_int(0, 2) ? random_int(0, 1000000) : null;
        $lead['budget'] = random_int(0, 2) ? random_int(0, 1000000) : null;

        return $lead;
    }

    public function downloadLeadsFile()
    {
        $fileName = 'Leads.json';

        $result['data'] = [];
        for ($i = 0; $i < 50; ++$i) {
            $result['data'][] = $this->generateLead();
        }

        Storage::disk('public')->put($fileName, json_encode($result));

        return response()->download(public_path($fileName));
    }

    public function importLeadsFile($fileData)
    {
        $data = json_decode($fileData);

        foreach ($data->data as $item) {
            $uploadData = [
                'transaction_name' => $item->transaction_name,
                'budget' => $item->budget, 'cost' => $item->cost
            ];
            $this->createLead($uploadData);
        }
    }
}
