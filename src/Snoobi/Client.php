<?php
namespace Snoobi;

use GuzzleHttp\Client AS GuzzleClient;
use GuzzleHttp\Exception\ParseException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Subscriber\Oauth\Oauth1;

class SnoobiApiException extends \Exception {}

class Client
{
    /**
     * Snoobi api url
     */
    const API_URL = 'https://api.snoobi.com/';

    /**
     * The GuzzleHttp\Client
     */
    private $client;

    /**
     * OAuth consumer key
     */
    private $consumerKey;

    /**
     * OAuth cosumer secret
     */
    private $consumerSecret;

    /**
     * OAuth access token
     */
    private $token;

    /**
     * OAuth token secret
     */
    private $tokenSecret;

    /**
     * @param array $params
     */
    public function __construct($params = [])
    {
        $this->consumerKey = isset($params['consumer_key']) ? $params['consumer_key'] : null;
        $this->consumerSecret = isset($params['consumer_secret']) ? $params['consumer_secret'] : null;
        $this->token = isset($params['token']) ? $params['token'] : null;
        $this->tokenSecret = isset($params['token_secret']) ? $params['token_secret'] : null;
    }

    /**
     * @param string $consumerKey
     */
    public function setConsumerKey($consumerKey)
    {
        $this->consumerKey = $consumerKey;
    }

    /**
     * @param string $consumerSecret
     */
    public function setConsumerSecret($consumerSecret)
    {
        $this->consumerSecret = $consumerSecret;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @param string $tokenSecret
     */
    public function setTokenSecret($tokenSecret)
    {
        $this->tokenSecret = $tokenSecret;
    }

    /**
     * Performs a GET request
     *
     * @param string $path
     * @return mixed
     */
    public function get($path)
    {
        return $this->doQuery('GET', $path);
    }

    /**
     * Performs a POST request
     *
     * @param string $path
     * @param array $payload
     * @return mixed
     */
    public function post($path, array $payload)
    {
        return $this->doQuery('POST', $path, $payload);
    }

    /**
     * Performs a PUT request
     *
     * @param string $path
     * @param array $payload
     * @return mixed
     */
    public function put($path, array $payload)
    {
        return $this->doQuery('PUT', $path, $payload);
    }

    /**
     * Performs a DELETE request
     *
     * @param string $path
     * @return mixed
     */
    public function delete($path)
    {
        return $this->doQuery('DELETE', $path);
    }

    /**
     * Performs a request and checks result
     *
     * @param string $type
     * @param string $path
     * @param array $payload
     */
    private function doQuery($type, $path, array $payload = null)
    {
        $options = ['auth' => 'oauth'];
        is_null($payload) ?: $options['json'] = $payload;

        $request = $this->getClient()->createRequest($type, $path, $options);

        try {
            $response = $this->getClient()->send($request);
        } catch(RequestException $e)
        {
            $response = $e->getResponse();
        }

        if ($response->getStatusCode() != 200)
            throw new SnoobiApiException((string)$response->getBody(), $response->getStatusCode());

        try{
            $body = $response->json();
        } catch (ParseException $e)
        {
            $body = (string)$response->getBody();
        }

        return $body;
    }

    /**
     * Initializes a Guzzle client and returns it
     *
     * @return GuzzleClient
     */
    private function getClient()
    {
        if (!is_null($this->client))
            return $this->client;

        $client = new GuzzleClient(['base_url' => self::API_URL]);
        $oauth = new Oauth1(array(
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret
        ));
        $client->getEmitter()->attach($oauth);
        return $this->client = $client;
    }
}
