<?php

namespace App\Services;

use Ramsey\Uuid\Uuid;
use AmoCRM\Models\CallModel;
use AmoCRM\Models\LeadModel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use AmoCRM\Models\CompanyModel;
use AmoCRM\Models\ContactModel;
use Dflydev\DotAccessData\Data;

use AmoCRM\Client\LongLivedAccessToken;
use Illuminate\Support\Facades\Storage;
use AmoCRM\EntitiesServices\CustomFields;
use AmoCRM\Exceptions\AmoCRMApiException;
use AmoCRM\Collections\ContactsCollection;
use function PHPUnit\Framework\fileExists;
use AmoCRM\Models\Interfaces\CallInterface;
use function PHPUnit\Framework\isInstanceOf;
use AmoCRM\Collections\Leads\LeadsCollection;

use AmoCRM\Collections\CustomFieldsValuesCollection;
use League\OAuth2\Client\Token\AccessTokenInterface;
use AmoCRM\Exceptions\AmoCRMApiErrorResponseException;
use AmoCRM\Collections\CustomFields\CustomFieldsCollection;
use AmoCRM\Models\CustomFieldsValues\BaseCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\NumericCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueModels\NumericCustomFieldValueModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\NumericCustomFieldValueCollection;

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
        // $leadsService = $this->apiClient->contacts();
        // $contact = $leadsService->getOne($data['transaction_id']);

        $call = new CallModel();
        $call
            ->setPhone('+79508395394') // кто звонил
            ->setCallStatus(CallInterface::CALL_STATUS_FAIL_NOT_PHONED)
            ->setCallResult('Разговор состоялся')
            ->setUniq(Uuid::uuid4())
            ->setLink('https://rus.hitmotop.com/get/music/20200919/Two_Plus_One_-_Barabara_Afro_Deep_Dub_Afro_Deep_Dub_70979590.mp3')
            ->setDuration(148)
            ->setSource('TestIntegration')
            ->setDirection(CallInterface::CALL_DIRECTION_IN)
            ->setCallResponsible('+79508395394');

        try {
            $call = $this->apiClient->calls()->addOne($call);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
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

    function printError(AmoCRMApiException $e): void
    {
        $errorTitle = $e->getTitle();
        $code = $e->getCode();
        $debugInfo = var_export($e->getLastRequestInfo(), true);

        $validationErrors = null;
        if ($e instanceof AmoCRMApiErrorResponseException) {
            $validationErrors = var_export($e->getValidationErrors(), true);
        }

        $error = <<<EOF
    Error: $errorTitle
    Code: $code
    Debug: $debugInfo
    EOF;

        if ($validationErrors !== null) {
            $error .= PHP_EOL . 'Validation-Errors: ' . $validationErrors . PHP_EOL;
        }

        echo '<pre>' . $error . '</pre>';
    }

    public function createCall()
    {
        $apiClient = new \AmoCRM\Client\AmoCRMApiClient();

        $longLivedAccessToken = new LongLivedAccessToken('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjJiYjE1MTJiYzk5OTQ2NmRjOTFhYzlhNGQwYTcwNzI3ZmI3OTFjMjQzMmRlODcyY2M2NzQ4YWIyN2MwZTBiNjRiMTZiMzBjM2I1M2MzNDJjIn0.eyJhdWQiOiJiODdkMjBlNS01N2FjLTQyNGQtYTZhOC02YTlkNWFmNmI1YzQiLCJqdGkiOiIyYmIxNTEyYmM5OTk0NjZkYzkxYWM5YTRkMGE3MDcyN2ZiNzkxYzI0MzJkZTg3MmNjNjc0OGFiMjdjMGUwYjY0YjE2YjMwYzNiNTNjMzQyYyIsImlhdCI6MTcxMTA0NDkxMCwibmJmIjoxNzExMDQ0OTEwLCJleHAiOjE3NDI1MTUyMDAsInN1YiI6IjEwODM0NDE0IiwiZ3JhbnRfdHlwZSI6IiIsImFjY291bnRfaWQiOjMxNjQ4Nzk4LCJiYXNlX2RvbWFpbiI6ImFtb2NybS5ydSIsInZlcnNpb24iOjIsInNjb3BlcyI6WyJjcm0iLCJmaWxlcyIsImZpbGVzX2RlbGV0ZSIsIm5vdGlmaWNhdGlvbnMiLCJwdXNoX25vdGlmaWNhdGlvbnMiXSwiaGFzaF91dWlkIjoiNDA1NzM3MmQtNTQxOC00NzFhLThiYzYtOTQyYTQzNmRkMmRmIn0.R4G-yD9M0QYRMye-2ifMIXrCJ0JVCMo3s59pLMcdGKcqe-5FlNCllCWFJdB7AuSdcUvTbw5zSvN_vtt0WwVdmncDHUpXym9V1aPbEXnwPL8PZLt-PY3_-ATlypVejE4cBhv0AAO89skUOcB3FLJrpo8PrShHJabHMbhWDrHzalFAeCrWPJJLO14suTUb0BjSnaMI2eIp8wQq_M7h8Wi5XBQUdSog8Cdksvr9sSWHvEztgSMtvK_wbSH5b8kK7Lp8_QxatGT3sqwplBFVv8r3dTGjJe7JABeDXj53B9RwF_CilW4M9jkt4daIxbt2LSW9QYtAVPLAomX1Dfz9ixAjfA');

        $apiClient->setAccessToken($longLivedAccessToken)->setAccountBaseDomain('derendaevkosta45.amocrm.ru');

        $call = new CallModel();
        $call
            ->setPhone('+78005853535') // кто звонил
            ->setCallStatus(CallInterface::CALL_STATUS_FAIL_NOT_PHONED)
            ->setCallResult('Разговор не состоялся')
            ->setUniq(Uuid::uuid4())
            ->setLink('https://rus.hitmotop.com/get/music/20200919/test.mp3')
            ->setDuration(1)
            ->setSource('TestIntegration')
            ->setDirection(CallInterface::CALL_DIRECTION_IN)
            ->setCallResponsible('+79508395394');
        // ->setEntityId('7803313');

        try {
            $call = $apiClient->calls()->addOne($call);
            dd($call);
        } catch (AmoCRMApiException $e) {
            $this->printError($e);
            die;
        }
    }
}
