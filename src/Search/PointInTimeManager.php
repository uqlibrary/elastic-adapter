<?php declare(strict_types=1);

namespace Elastic\Adapter\Search;

use Elastic\Adapter\Client;
use GuzzleHttp\Ring\Future\FutureArrayInterface as Elasticsearch;

final class PointInTimeManager
{
    use Client;

    public function open(string $indexName, ?string $keepAlive = null): string
    {
        $params = ['index' => $indexName];

        if (isset($keepAlive)) {
            $params['keep_alive'] = $keepAlive;
        }

        /** @var Elasticsearch $response */
        $rawResult = $this->client->createPointInTime($params);

        return $rawResult['pit_id'];
    }

    public function close(string $pointInTimeId): self
    {
        $this->client->deletePointInTime([
            'body' => [
                'pit_id' => $pointInTimeId,
            ],
        ]);

        return $this;
    }
}
