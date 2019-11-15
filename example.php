<?php

require_once __DIR__.'/vendor/autoload.php';

$account = 'z9GRRu7FxmO53CQ9cFfI6qiy32wpfTkd';
$secret = 'w24JTCv4MnAcuRTx0oHjHLDtyt3I6IBq';

$client = new \shevernitskiy\CdekApi\Client($account, $secret);

// Ручная авторизация, в нормальном режиме ее делать не нужно
//$result = $client->auth();

// Любой запрос к апи
//$result = $client->sendRequest('GET', '/v2/location/regions', $payload);

// Список городов
/*$result = $client->getCities([
    'country_codes' => ['ru'],
    'size' => 3,
    'region_code' => 23,
]);*/

// Список регионов
/*$result = $client->getRegions([
    "country_codes" => ["ru", "kz"],
    "size" => 3,
]);*/

// Получения вебхуков
//$result = $client->getWebhook();

// Добавление вебхука
/*$result = $client->addWebhook([
    'url' => 'https://www.webhook.site/6477b228-352d-443a-9145-411f0e2e27be',
    'type' => 'ORDER_STATUS',
]);*/

// Удаление вебхука
//$result = $client->delWebhook('a6e5ebea-792a-4c5a-8206-ce1ce4819fd0');

// Использование справочника
//echo PHP_EOL.\shevernitskiy\CdekApi\Data::orderStatus(1);

echo PHP_EOL.'==============================='.PHP_EOL;
echo 'result: '.$result;
echo PHP_EOL.'end example.php';
