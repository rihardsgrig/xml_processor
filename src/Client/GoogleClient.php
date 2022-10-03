<?php

namespace Xml\Processor\Client;

use Google\Client;
use ReflectionClass;
use Xml\Processor\Exception\UnknownServiceException;

class GoogleClient
{
    private Client $client;

    /**
     * @param array{application_name?: string, google_api_creds?: string, scopes?: array<string>} $config
     */
    public function __construct(array $config)
    {
        $this->client = new Client();
        $this->client->setApplicationName($config['application_name'] ?? '');
        $this->client->setAccessType('offline');
        $this->client->setAuthConfig($config['google_api_creds'] ?? '');
        $this->client->setScopes($config['scopes'] ?? []);
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function make(string $service)
    {
        $service = 'Google\\Service\\' . ucfirst($service);

        if (class_exists($service)) {
            $class = new ReflectionClass($service);

            return $class->newInstance($this->client);
        }

        throw UnknownServiceException::fromServiceName($service);
    }
}
