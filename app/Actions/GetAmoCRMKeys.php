<?php

namespace App\Actions;

class GetAmoCRMKeys
{
    public function handle()
    {
        $subdomain     = 'derendaevkosta45'; // поддомен AmoCRM
        $client_secret = 'k6K1MZPelPL65jhF8CqkmLPqU0GDuqxRdi6nCAShgm8tZY7eU8MX1rSIRSRUCcB2'; // Секретный ключ
        $client_id     = '599527ba-d9c3-4704-a8bc-9e2683df3b0b'; // ID интеграции
        $code          = 'def50200653ed6326be108b9b133b1dcbf15ecbb355b1633b8c96d78de371bf910e2b3b9
        dbd25b44de0539e31cbaa06aaef5a805d43bcd7341477084482c0a21b296d1a82a1d6d2cf33fa8bb80e930b02c
        a032ac46497a37b1d8f661e443ec1909079561a5f885447409849c518ee0457461ad63d7c303deeebc03ada03f
        ecbaae3ce00043c14b0bbd38053b66005e660402b942fcd3f138bde6f1888c8b37dfe021da00b0851be1b45041
        ea27e96c09785ff859f382bf2489eb4facce07962b5cc41c5e83ea19aac1972c0cb7685bd71481448012648682
        eada7dfbfb4e22cd2dcbad9c2bfa2335e23868519bca0bfda1163dc8b101d834184d2aa3e4a4bdc63f0a6d8a06
        a0a75f85e9b97aa6a9ca92e70069cefd5631ac57605348e9efce0cae347891414d624876e40bc3bbd409d0684a
        4e92a829f6bf5e7426679a363e9071201d7757848d8f25f961455547caa1c5b7a1e8eb5028b89e5a44d7957e94
        e2d61b1ae231664e451f98109f8beec671b6838249e4d5c8efee15785cd246d88ab1d1572dabd8e2a2c3831a94
        7eab05362fe46ce544e021028b5e617a33aed94de2780f1fb2f61ab10e990511f2b064a115d0f9c35a2054e1e5
        7ca63b38ca605eb5fe6a700ffdd31bb264a4891e9f48bd05cc8e9e8c1d374a5ba8179ef324911bf6322c4ba08b
        71209b789f8d1eb751501d0a7f44792a22ac7adbb51d68b6763fe51b9508f8962ec467e5e8b0ed2aed4f'; // Код авторизации
        $token_file    = 'tokens.txt';
        $redirect_uri  = 'https://derfing.ru/';

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

        if ($code < 200 || $code > 204) die("Error $code. " . (isset($errors[$code]) ? $errors[$code] : 'Undefined error'));


        $response = json_decode($out, true);

        $arrParamsAmo = [
            "access_token"  => $response['access_token'],
            "refresh_token" => $response['refresh_token'],
            "token_type"    => $response['token_type'],
            "expires_in"    => $response['expires_in'],
            "endTokenTime"  => $response['expires_in'] + time(),
        ];

        $arrParamsAmo = json_encode($arrParamsAmo);

        $f = fopen($token_file, 'w');
        fwrite($f, $arrParamsAmo);
        fclose($f);

        print_r($arrParamsAmo);
    }
}
