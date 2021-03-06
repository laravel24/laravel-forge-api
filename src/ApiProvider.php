<?php

namespace Laravel\Forge;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

class ApiProvider
{
    /**
     * Base API URI.
     *
     * @var string
     */
    const BASE_URI = 'https://forge.laravel.com/api/v1/';

    /**
     * API token.
     *
     * @var string
     */
    private $token;

    /**
     * HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * Create new API provider instance.
     *
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * HTTP client.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function getClient(): ClientInterface
    {
        if (!is_null($this->client)) {
            return $this->client;
        }

        return $this->client = $this->createClient();
    }

    /**
     * API token.
     *
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * Create new HTTP client.
     *
     * @return \GuzzleHttp\ClientInterface
     */
    public function createClient(): ClientInterface
    {
        $client = new Client([
            'base_uri' => static::BASE_URI,
            'headers' => [
                'Authorization' => 'Bearer '.$this->getToken(),
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        return $client;
    }
}
