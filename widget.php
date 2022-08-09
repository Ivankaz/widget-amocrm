<?php
// поддомен аккаунта
$subdomain = 'ivankaz9';
// URL запроса к API amoCRM
// получаю товары (элементы списка Products)
$link = 'https://' . $subdomain . '.amocrm.com/api/v4/catalogs/1120/elements';
// получаю access token из файла
$access_token = file_get_contents('access_token.txt');
// заголовки, в которых передаю access token
$headers = [
    'Authorization: Bearer ' . $access_token
];

// делаю запрос к серверу amoCRM
$curl = curl_init();
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
curl_setopt($curl, CURLOPT_URL, $link);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl);
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);

// ответ сервера
$out = json_decode($out, true);
// код ответа сервера
$code = (int)$code;

// описание ошибок
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

// обрабатываю ответ от сервера
try {
    // если ответ от сервера неверный, вывожу ошибку
    if ($code < 200 && $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }

    print_r($out);
} catch (\Exception $e) {
    die('Error: ' . $e->getMessage() . PHP_EOL . 'Error code: ' . $e->getCode());
}