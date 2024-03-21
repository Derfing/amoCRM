<?php

namespace App\Actions;

use Exception;

class GetAmoCRMKeys
{
    public function handle()
    {
        $subdomain     = 'derendaevkosta45'; // поддомен AmoCRM
        $client_secret = '1OMQwvyjZL6cZ0h5rm61RkDlgCKnVQVoI9ifI9choX56tqprvCcTyfRVfa7hK3d2'; // Секретный ключ
        $client_id     = 'b87d20e5-57ac-424d-a6a8-6a9d5af6b5c4'; // ID интеграции
        $code          = 'def50200b83fedef2ef5d797d787f78239f0c12e697c5c48e4ab1ad53764a686fe798d5961522dbfb77a91c0a86f8279c21a2cf9617c34dcbedf1e2fa7b79edae407e7b057cbf38df809fc376037b170e51a51ded30d13b00d6a5ec46cce17d2cc88ea1275a33396ed725b4774bff55eaf166b493d5f5438bd9db1bd4e34636955bd4ee3d8b2746325842575ff52697af39710985030f38680e660829bcab2aec9cabb92432913ef089fe2d809f6f8c5bb8882e178b77646949009eeae2940eb4c9032885b1d9c95f25b8e6dc42de9759448386f351a82156d02d44cc78c05d1cb2110f3fc4c7f5c7a276fcda20212aa3ecaae18f9ccb83d72dcef76f1cbbe38e24c5dacbeb063785cc06ae7af01006b72cf67400429f012a7b1b62eff5c0c97571b7269595ab22fa508d4b91fbce8b6471fd9455e89f8849e2428d6e9517be6821f11b6e21f34022fe84fd33e8d88121367d87d034308c32b5288d02bc9704c01758e59b66f6fd2fbbd6aebfeb62f2ba27e3639d0308eda316dd468c6a431b5e9688ab93d157589108019173cb9df2925c6af4f4788744255cab729b350d5032816acea4c6483410974ca02e4358a163054f0452e4a71de7f8576ddb30ac91fa97b7398ead57da9b94ac1f32d5875fce46f0d5d3c48848583692841f10c948594a41e'; // Код авторизации
        $token_file    = 'tokens.txt';
        $redirect_uri  = 'https://derfing.ru';

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

        $arrParamsAmo = [
            "access_token"  => $response['access_token'],
            "refresh_token" => $response['refresh_token'],
            "token_type"    => $response['token_type'],
            "expires_in"    => $response['expires_in']
        ];

        $arrParamsAmo = json_encode($arrParamsAmo);

        $f = fopen($token_file, 'w');
        fwrite($f, $arrParamsAmo);
        fclose($f);

        print_r($arrParamsAmo);
    }
}
