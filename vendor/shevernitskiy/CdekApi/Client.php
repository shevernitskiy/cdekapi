<?php

/**
 * @version 0.2
 *
 * @uses GuzzleHttp
 * @uses Climate
 * @throws Exception
 */

namespace shevernitskiy\CdekApi;

class Client
{
    const URL_API = 'http://api.edu.cdek.ru';
    const GRANT_TYPE = 'client_credentials';
    const TOKEN_STORAGE = 'token.txt';
    const DEBUG = true;

    protected $account;
    protected $secret;
    protected $token;
    protected $tokenExpires;

    public function __construct($account, $secret)
    {
        if (empty($account) || empty($secret)) {
            throw new \Exception('ERROR: not valid credentials');
        }
        $this->account = $account;
        $this->secret = $secret;
        self::debug('DEBUG MODE');
        if (file_exists(self::TOKEN_STORAGE)) {
            self::debug('Token storage found, reading data...');
            $result = $this->readToken();
            if ($result) {
                if (!$this->validToken()) {
                    self::debug('Token expired, refreshing...');
                    $result = $this->refreshToken();
                } else {
                    self::debug('Token valid');
                }
            }
        } else {
            self::debug('Token storage not found, start new auth procedure...');
            $result = $this->auth();
            if (!$result) {
                throw new \Exception('ERROR: cant auth in constructor');
            }
        }
    }

    /**
     * Функция авторизации.
     */
    public function auth()
    {
        if (empty($this->account) || empty($this->secret)) {
            throw new \Exception('ERROR: not valid credentials');
        }
        $client = new \GuzzleHttp\Client([
            'base_uri' => self::URL_API,
        ]);
        try {
            $response = $client->request('POST', '/v2/oauth/token?parameters', [
                'form_params' => [
                    'grant_type' => self::GRANT_TYPE,
                    'client_id' => $this->account,
                    'client_secret' => $this->secret,
                ],
            ]);
            if (self::isJson($response->getBody())) {
                self::debug('Auth JSON response:'.PHP_EOL.self::jsonPrettify($response->getBody()));
            } else {
                self::debug('Auth Response:'.PHP_EOL.$response->getBody());
            }
            if ($response->getStatusCode() == 200) {
                $this->storeToken($response->getBody());
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            self::DEBUG ? self::printException($e) : '';
            return ($e->getResponse())->getBody();
        }
        return true;
    }

    /**
     * Функция отправки запроса к апи.
     *
     * @param string $type    - GET, POST, PUT, DELET
     * @param string $url     - url до метода без base_uri
     * @param array  $payload - тело запроса
     *
     * К запросу добавляется header c авторизацией и контент тайпом
     * $payload кодируется в json
     *
     * @return string response
     * @return bool   false
     */
    public function sendRequest($type, $url, $payload = null)
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => self::URL_API,
        ]);
        $request = [
            'headers' => [
                'Authorization' => 'Bearer '.$this->token,
            ],
        ];
        if ($payload != null) {
            $request['headers']['Content-Type'] = 'application/json';
            $request['body'] = json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }
        try {
            self::debug('Making request to '.$url);
            $response = $client->request($type, $url, $request);
            if (self::isJson($response->getBody())) {
                self::debug('JSON response:'.PHP_EOL.self::jsonPrettify($response->getBody()));
            } else {
                self::debug('Response:'.PHP_EOL.$response->getBody());
            }
            return $response->getBody();
        } catch (\GuzzleHttp\Exception\ServerException $e) {
            self::DEBUG ? self::printException($e) : '';
            if (self::isJson(($e->getResponse())->getBody())) {
                return self::jsonPrettify(($e->getResponse())->getBody());
            } else {
                return ($e->getResponse())->getBody();
            }
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            self::DEBUG ? self::printException($e) : '';
            if (self::isJson(($e->getResponse())->getBody())) {
                return self::jsonPrettify(($e->getResponse())->getBody());
            } else {
                return ($e->getResponse())->getBody();
            }
        }
        return true;
    }

    /**
     * Функциии сохранения токена в файл.
     *
     * В процессе записи добавлет поле 'expires' с таймстемпом истечения
     *
     * @param string $json - json строка с информацией о токене
     */
    private function storeToken($json)
    {
        $array = json_decode($json, true);
        $array['expires'] = time() + $array['expires_in'];
        $this->token = $array['access_token'];
        $this->tokenExpires = $array['expires'];
        if (!empty(self::TOKEN_STORAGE)) {
            if (file_put_contents(self::TOKEN_STORAGE, json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES))) {
                self::debug('Token stored to: '.self::TOKEN_STORAGE);
            } else {
                throw new \Exception('ERROR: cant write token to file');
            }
        } else {
            throw new \Exception('ERROR: token storage must be specified');
        }
    }

    /**
     * Функция чтения токена из файла.
     *
     * Заполняет соответсвующие переменные объекта
     */
    private function readToken()
    {
        if (file_exists(self::TOKEN_STORAGE)) {
            $json = file_get_contents(self::TOKEN_STORAGE);
            $array = json_decode($json, true);
            $this->token = $array['access_token'];
            $this->tokenExpires = $array['expires'];
            self::debug('Token read from storage: '.PHP_EOL.$this->token);
        } else {
            throw new \Exception('ERROR: cant read token from storage '.self::TOKEN_STORAGE);
            return false;
        }
        return true;
    }

    /**
     * Функция обновления токена.
     *
     * Ищет сохраненный в файле токен, проверяет на валидность, обновляет при необходимости
     */
    private function refreshToken()
    {
        if (empty($this->token) || empty($this->tokenExpires)) {
            self::debug('Read token from storage...');
            if (!$this->readToken()) {
                return false;
            }
        }
        if ($this->token && $this->tokenExpires) {
            if ($this->validToken()) {
                self::debug('Token still valid for: '.($this->tokenExpires - time()).'s');
                return true;
            } else {
                $result = $this->auth();
                if ($result) {
                    self::debug('Token refreshed, valid for: '.($this->tokenExpires - time()).'s');
                    return true;
                } else {
                    throw new \Exception('ERROR: cant refresh token, some shit happens');
                }
            }
        } else {
            throw new \Exception('ERROR: cant refresh token because it does not set, need call auth()');
            return false;
        }
    }

    public function validToken()
    {
        if ($this->tokenExpires > time()) {
            return true;
        }
        return false;
    }

    protected function debug($msg)
    {
        if (self::DEBUG) {
            echo $msg.PHP_EOL;
        }
        return true;
    }

    protected function isJson($str)
    {
        json_decode($str);
        return json_last_error() == JSON_ERROR_NONE;
    }

    protected function jsonPrettify($json)
    {
        return json_encode(json_decode($json, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    protected function printException($e)
    {
        echo 'ERROR: request error occured'.PHP_EOL.PHP_EOL.'Request:'.PHP_EOL;
        echo \GuzzleHttp\Psr7\str($e->getRequest());
        echo PHP_EOL.PHP_EOL.'Response:'.PHP_EOL;
        echo \GuzzleHttp\Psr7\str($e->getResponse());
    }

    public function getRegions(array $array)
    {
        if (is_array($array)) {
            $result = $this->sendRequest('GET', '/v2/location/regions', $array);
            return $result;
        } else {
            throw new \Exception('ERROR: not valid array');
            return false;
        }
    }

    public function getCities(array $array)
    {
        if (is_array($array)) {
            $result = $this->sendRequest('GET', '/v2/location/cities', $array);
            return $result;
        } else {
            throw new \Exception('ERROR: not valid array');
            return false;
        }
    }

    public function addOrder(array $array)
    {
        if (is_array($array)) {
            $result = $this->sendRequest('POST', '/v2/orders', $array);
            return $result;
        } else {
            throw new \Exception('ERROR: not valid array');
            return false;
        }
    }

    public function getOrder(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('GET', '/v2/orders/'.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }

    public function delOrder(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('DELETE', '/v2/orders/'.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }

    public function addCourier(array $array)
    {
        if (is_array($array)) {
            $result = $this->sendRequest('POST', '/v2/intakes', $array);
            return $result;
        } else {
            throw new \Exception('ERROR: not valid array');
            return false;
        }
    }

    public function getCourier(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('GET', '/v2/intakes/'.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }

    public function delCourier(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('DELETE', '/v2/intakes/'.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }

    public function addWebhook(array $array)
    {
        if (is_array($array)) {
            $result = $this->sendRequest('POST', '/v2/webhooks', $array);
            return $result;
        } else {
            throw new \Exception('ERROR: not valid array');
            return false;
        }
    }

    public function getWebhook(string $uuid = null)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('GET', '/v2/webhooks/'.$uuid);
            return $result;
        } elseif (empty($uuid)) {
            $result = $this->sendRequest('GET', '/v2/webhooks');
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id or empty');
            return false;
        }
    }

    public function delWebhook(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('DELETE', '/v2/webhooks/'.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }

    public function getPay(string $uuid)
    {
        if (!empty($uuid) && is_string($uuid)) {
            $result = $this->sendRequest('GET', '/v2/payments/search?cdekNumber='.$uuid);
            return $result;
        } else {
            throw new \Exception('ERROR: uuid should be valid string id');
            return false;
        }
    }
}
