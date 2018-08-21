<?php
namespace ShoppingFeed\Sdk\Client;

use ShoppingFeed\Sdk\Credential\CredentialInterface;
use ShoppingFeed\Sdk\Hal;
use ShoppingFeed\Sdk\Http;

class Client
{
    const VERSION = '1.0.0';

    /**
     * @var Hal\HalClient
     */
    private $client;

    /**
     * @var array
     */
    private $sdkHeaders = [
        'Accept'          => 'application/json',
        'User-Agent'      => 'SF-SDK-PHP/' . Client::VERSION,
        'Accept-Encoding' => 'gzip',
    ];

    /**
     * @param CredentialInterface $credential
     * @param ClientOptions|null  $options
     *
     * @return \ShoppingFeed\Sdk\Api\Session\SessionResource
     */
    public static function createSession(CredentialInterface $credential, ClientOptions $options = null)
    {
        return (new self($options))->authenticate($credential);
    }

    public function __construct(ClientOptions $options = null)
    {
        if (null === $options) {
            $options = new ClientOptions();
        }

        $options->setHeaders(array_merge($options->getHeaders(), $this->sdkHeaders));

        if (null === $options->getHttpAdapter()) {
            $options->setHttpAdapter(new Http\Adapter\Guzzle6Adapter($options));
        }

        $this->client = new Hal\HalClient(
            $options->getBaseUri(),
            $options->getHttpAdapter()
        );
    }

    /**
     * @return Hal\HalClient
     */
    public function getHalClient()
    {
        return $this->client;
    }

    /**
     * Ping APi
     *
     * @return bool
     */
    public function ping()
    {
        return (bool) $this
            ->getHalClient()
            ->request('GET', 'v1/ping')
            ->getProperty('timestamp');
    }

    /**
     * @param CredentialInterface $credential
     *
     * @return \ShoppingFeed\Sdk\Api\Session\SessionResource
     */
    public function authenticate(CredentialInterface $credential)
    {
        return $credential->authenticate($this->client);
    }
}
