<?php declare(strict_types=1);

namespace Elastic\Adapter;

use Elastic\Client\ClientBuilderInterface as ElasticClientBuilderInterface;
use OpenSearch\Client as ElasticClient;

trait Client
{
    private ElasticClientBuilderInterface $clientBuilder;
    private ElasticClient $client;

    public function __construct(ElasticClientBuilderInterface $clientBuilder)
    {
        $this->clientBuilder = $clientBuilder;
        $this->client = $clientBuilder->default();
    }

    public function connection(string $name): self
    {
        $self = clone $this;
        $self->client = $self->clientBuilder->connection($name);
        return $self;
    }
}
