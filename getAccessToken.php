<?php
// поддомен аккаунта
$subdomain = 'ivankaz9';
// URL запроса
$link = 'https://' . $subdomain . '.amocrm.com/oauth2/access_token';
// данные для запроса
$data = [
    'client_id' => 'xxxx',
    'client_secret' => 'xxxx',
    'grant_type' => 'authorization_code',
    'code' => 'xxxx',
    'redirect_uri' => 'https://ivankaz9.amocrm.com',
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
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

$code = (int)$code;
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

print_r(json_decode($out, true));

/*
try {
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
} catch (Exception $e) {
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}

$response = json_decode($out, true);

$access_token = $response['access_token']; // Access токен
$refresh_token = $response['refresh_token']; // Refresh токен
$token_type = $response['token_type']; // Тип токена
$expires_in = $response['expires_in']; // Через сколько действие токена истекает
*/