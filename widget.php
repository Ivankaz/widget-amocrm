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

// разрешаю запросы с поддомена своего аккаунта на amoCRM
header("Access-Control-Allow-Origin: https://$subdomain.amocrm.com");

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

/**
 * Функция для получения значения поля элемента списка
 * @param $customFieldsValues
 * @param $customFieldCode
 * @return mixed|null
 */
function getCustomFieldValue($customFieldsValues = [], $customFieldCode = '') {
    // возвращаемое значение
    $value = null;

    // ищу поле по его коду
    foreach ($customFieldsValues as $custom_field) {
        if ($custom_field['field_code'] == $customFieldCode) {
            $value = $custom_field['values'][0]['value'];
        }
    }

    return $value;
}

/**
 * Функция получения информации о товаре
 * @param $product
 * @return array
 */
function getProduct($product) {
    // название товара
    $name = $product['name'];
    // количество товара
    $quantity = getCustomFieldValue($product['custom_fields_values'], 'QUANTITY');

    // возвращаю информацию о товаре
    return [
        'name' => $name,
        'quantity' => $quantity,
    ];
}

// обрабатываю ответ от сервера
try {
    // если ответ от сервера неверный, вывожу ошибку
    if ($code < 200 && $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }

    // список товаров
    $products = array_map('getProduct', $out['_embedded']['elements']);

    // вывожу список товаров в формате JSON
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    die('Error: ' . $e->getMessage() . PHP_EOL . 'Error code: ' . $e->getCode());
}