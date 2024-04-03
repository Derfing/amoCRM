<?php

namespace App\Actions;

use Exception;
use Illuminate\Support\Facades\Storage;

class GetAmoCRMKeys
{
    public function handle()
    {
        $subdomain     = 'derendaevkosta45'; // поддомен AmoCRM
        $client_secret = 'Rv8GF1sOOfKRd4fCfVHT7StRKtx1zz2bJG2AgGuJlS2UkdzMM2vMmts5VkRxvLrP'; // Секретный ключ
        $client_id     = '37266887-ca73-42f1-9bc8-7cfea67efd4d'; // ID интеграции
        $code          = 'def50200521ca258999f95502d3aab8970aaeb5f1e2f42399b50ec00b0dfcef8338bf1846827750fac35a0d46b48fe24ee1227adc4422cf4d495312cd1f56c26de0fb02664124cdc0488e9977e961e8801e733bfc2fea14cf3a6f87698ee4c860f3d8dedda71a2dee7819f483247a585d84105d0326341210073f5f591263e14379245408c5f99cf013120fffb8246958147b3a6135981eb2d3603fdf4d737a8b3645c8726390e133feaf2b4745266d1462673bea8b93da54a9550bd1de080164d836a53fce256ffb14a2cbbe9de66996269c94f36290c9c1d5adf6c5a61378a1b00929f03f13f8eca3a766e633a752e5b1094278ccd2be901409d371b77f4fc844ff2e73cdb4d5600a55c3a4bf117ac816201ae2821a65981622de626c638c07bf8486fe71f1f6a3335d8ccdb801b932b897f46e7720a9095b8fdac97fe711e6cbbca638a3c23c0844ee3f095c862b1893ca845c539e70f73e183d9645ab91715136d7cc15b55407b78f847039fdd32acdbbcf211916091ace404841b569bca51a1acdcf0046704fb5e8aec343b88087abcd97c756601945891f7e616dc57b718414e4c936261bff1ff160b6ae2321c7092b40796a2a7b1d85b5863d022e041924a2628068c646781170de177d2fb0b26f2597562a8b376ff5db7efa18b57e5285bb99af6217c20c4db1f6feed2138d6528a06ad01fb3d8894a9f02e5d6db0747dd3977c8d59f43f21f1b'; // Код авторизации
        $redirect_uri  = 'https://webhook.site/b185dfec-dcfe-43be-bf6b-1092448b6d80';

        $link = "https://$subdomain.amocrm.ru/oauth2/access_token";

        $data = [
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $redirect_uri,
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
        curl_setopt($curl, CURLOPT_URL, $link);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        $code = (int)$code;

        $errors = [
            301 => 'Moved permanently.',
            400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
            401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
            403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
            404 => 'Not found.',
            500 => 'Internal server error.',
            502 => 'Bad gateway.',
            503 => 'Service unavailable.'
        ];

        try {
            /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
            if ($code < 200 || $code > 204) {
                throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
            }
        } catch (\Exception $e) {
            die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
        }

        $response = json_decode($out, true);

        $data = [
            "access_token"  => $response['access_token'],
            "refresh_token" => $response['refresh_token'],
            "token_type"    => $response['token_type'],
            "expires_in"    => $response['expires_in']
        ];

        $jsonData = json_encode($data);

        Storage::disk('local')->put('data.json', $jsonData);
    }
}
