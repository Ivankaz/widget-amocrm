<?php
// поддомен аккаунта
$subdomain = 'ivankaz9';
// разрешаю запросы с поддомена своего аккаунта на amoCRM
header("Access-Control-Allow-Origin: https://$subdomain.amocrm.com");

// ID сделки
$lead_id = filter_input(INPUT_GET, 'lead_id', FILTER_VALIDATE_INT);
// URL запроса к API amoCRM для получения информации о сделке
$link = 'https://' . $subdomain . '.amocrm.com/api/v4/leads/' . $lead_id . '?with=catalog_elements';
// получаю access token из файла
$access_token = file_get_contents('access_token.txt');
// заголовки, в которых передаю access token
$headers = [
    'Authorization: Bearer ' . $access_token
];

// получить результат запроса
function getUrl($link)
{
    global $headers;

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
    return json_decode($out, true);
}

/**
 * Функция для получения значения поля элемента списка
 * @param $customFieldsValues
 * @param $customFieldCode
 * @return mixed|null
 */
function getCustomFieldValue($customFieldsValues = [], $customFieldCode = '')
{
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
 * Функция получения названия товара
 * @param $catalog_id
 * @param $product_id
 * @return mixed
 */
function getProductName($catalog_id, $product_id)
{
    global $subdomain;
    $out = getUrl('https://' . $subdomain . '.amocrm.com/api/v4/catalogs/' . $catalog_id . '/elements/' . $product_id);
    return $out['name'];
}

/**
 * Функция получения информации о товаре
 * @param $product
 * @return array
 */
function getProduct($product)
{
    // ID списка
    $catalog_id = $product['metadata']['catalog_id'];
    // ID товара
    $product_id = $product['id'];

    // название товара
    $name = getProductName($catalog_id, $product_id);
    // количество товара
    $quantity = $product['metadata']['quantity'];

    // возвращаю информацию о товаре
    return [
        'name' => $name,
        'quantity' => $quantity,
    ];
}

// получаю информацию о сделке
$out = getUrl($link);

// обрабатываю ответ от сервера
try {
    $products = array_map('getProduct', $out['_embedded']['catalog_elements']);

    // вывожу список товаров в формате JSON
    echo json_encode($products, JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    die('Error: ' . $e->getMessage() . PHP_EOL);
}