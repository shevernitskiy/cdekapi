<?php

require_once __DIR__.'/vendor/autoload.php';

$account = 'z9GRRu7FxmO53CQ9cFfI6qiy32wpfTkd';      // тестовые логин
$secret = 'w24JTCv4MnAcuRTx0oHjHLDtyt3I6IBq';       // пароль

$client = new \shevernitskiy\CdekApi\Client($account, $secret);
$result = '';

/* ------- Ручная авторизация, в нормальном режиме ее делать не нужно ------- */

//$result = $client->auth();

/* --------------------------- Любой запрос к апи --------------------------- */

//$result = $client->sendRequest('GET', '/v2/location/regions', $payload);

/* ----------------------------- Список городов ----------------------------- */
/*
$result = $client->getCities([
    'country_codes' => ['ru'],
    'size' => 3,
    'region_code' => 23,
]);
*/
/* ----------------------------- Список регионов ---------------------------- */
/*
$result = $client->getRegions([
    "country_codes" => ["ru", "kz"],
    "size" => 3,
]);
*/
/* --------------------------- Получения вебхуков --------------------------- */

//$result = $client->getWebhook();

/* --------------------------- Добавление вебхука --------------------------- */
/*
$result = $client->addWebhook([
    'url' => 'https://www.webhook.site/6477b228-352d-443a-9145-411f0e2e27be',
    'type' => 'ORDER_STATUS',
]);
*/
/* ---------------------------- Удаление вебхука ---------------------------- */

//$result = $client->delWebhook('a6e5ebea-792a-4c5a-8206-ce1ce4819fd0');

/* --------------------------- Создание заказа ИМ --------------------------- */
/*
$result = $client->addOrder([
    'number' => 'ddOererre7450813980068',
    'comment' => 'Новый заказ',
    'delivery_recipient_cost' => [
        'value' => 500,
    ],
    'delivery_recipient_cost_adv' => [
        [
            'sum' => 3000,
            'threshold' => 200,
        ],
    ],
    'from_location' => [
        'code' => '44',
        'fias_guid' => '',
        'postal_code' => '',
        'longitude' => '',
        'latitude' => '',
        'country_code' => '',
        'region' => '',
        'sub_region' => '',
        'city' => 'Москва',
        'kladr_code' => '',
        'address' => 'пр. Ленинградский, д.4',
    ],
    'to_location' => [
        'code' => '270',
        'fias_guid' => '',
        'postal_code' => '',
        'longitude' => '',
        'latitude' => '',
        'country_code' => '',
        'region' => '',
        'sub_region' => '',
        'city' => 'Новосибирск',
        'kladr_code' => '',
        'address' => 'ул. Блюхера, 32',
    ],
    'items_cost_currency' => 'RUB',
    'packages' => [
        [
            'comment' => 'Упаковка',
            'height' => 10,
            'items' => [
                [
                    'ware_key' => '00055',
                    'payment' => [
                        'value' => 3000,
                    ],
                    'name' => 'Товар',
                    'cost' => 300,
                    'amount' => 2,
                    'weight' => 700,
                    'url' => 'www.item.ru',
                ],
            ],
            'length' => 10,
            'number' => '0123456',
            'weight' => 4000,
            'width' => 10,
        ],
    ],
    'recipient' => [
        'name' => 'Иванов Иван',
        'phones' => [
            [
                'number' => '+79134637228',
            ],
        ],
    ],
    'recipient_currency' => 'RUB',
    'sender' => [
        'name' => 'Петров Петр',
    ],
    'services' => [
        [
            'code' => 'DELIV_WEEKEND',
        ],
    ],
    'tariff_code' => 137,
]);
*/
/* ---------------------------- Получение заказа ---------------------------- */

//$result = $client->getOrder('72753034-f18a-4183-becf-abe69be02db6');

/* ----------------------------- Удаление заказа ---------------------------- */

//$result = $client->getOrder('72753034-f18a-4183-becf-abe69be02db6');

/* ------------------------ Использование справочника ----------------------- */

//echo PHP_EOL.\shevernitskiy\CdekApi\Data::orderStatus(1);

echo PHP_EOL.'==============================='.PHP_EOL;
echo 'result: '.PHP_EOL.$result;
echo PHP_EOL.'end of example.php';
