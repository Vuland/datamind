<?php

declare(strict_types=1);

namespace Modules\Aggregation\Services;

use Elasticsearch\Client;
use Modules\Aggregation\Interfaces\AggregationReportServiceInterface;
use Modules\Index\Interfaces\EsClientFactoryInterface;

final class EsAggregationReportService implements AggregationReportServiceInterface
{
    public function __construct(
        private readonly EsClientFactoryInterface $esFactory,
    ) {
    }

    public function generate(string $host, string $index, int $pageSize = 1000): iterable
    {
        $client = $this->esFactory->create($host);

        $afterKey = null;
        while (true) {
            $result = $this->retrieveAggPage($client, $index, $pageSize, $afterKey);
            $aggregation = $result['aggregations']['by_area_product'];

            foreach ($aggregation['buckets'] as $bucket) {
                yield [
                    '_id' => [
                        'area' => $bucket['key']['area'],
                        'product' => $bucket['key']['product'],
                    ],
                    'totalQuantity' => $bucket['totalQuantity']['value'],
                ];
            }

            $afterKey = $aggregation['after_key'] ?? null;
            if ($afterKey === null) {
                break;
            }
        }
    }

    private function retrieveAggPage(Client $client, string $index, int $pageSize, ?array $afterKey): array
    {
        $composite = [
            'size' => $pageSize,
            'sources' => [
                ['area' => ['terms' => ['field' => 'area']]],
                ['product' => ['terms' => ['field' => 'product']]],
            ],
        ];

        if ($afterKey !== null) {
            $composite['after'] = $afterKey;
        }

        return $client->search([
            'index' => $index,
            'body' => [
                'size' => 0,
                'aggs' => [
                    'by_area_product' => [
                        'composite' => $composite,
                        'aggs' => [
                            'totalQuantity' => [
                                'sum' => ['field' => 'quantity'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}

