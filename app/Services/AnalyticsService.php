<?php

namespace App\Services;

use Google\Analytics\Data\V1beta\Client\BetaAnalyticsDataClient;
use Google\Analytics\Data\V1beta\RunReportRequest;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\Dimension;
use App\Models\ProductViewLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AnalyticsService
{
    protected $client;
    protected $propertyId;

    public function __construct()
    {
        $this->client = new BetaAnalyticsDataClient([
            'credentials' => storage_path('app/ga-credentials.json'),
        ]);
        // Use property id from env or fallback to default
        $this->propertyId = env('GA4_PROPERTY_ID', 'properties/489895424');
    }

    public function getSessionsLast7Days(): int
    {
        return Cache::remember('ga_sessions_last_7_days', 3600, function () {
            $response = $this->client->runReport(new RunReportRequest([
                'property' => $this->propertyId,
                'date_ranges' => [
                    new DateRange(['start_date' => '7daysAgo', 'end_date' => 'today']),
                ],
                'metrics' => [new Metric(['name' => 'sessions'])],
            ]));

            return (int) $response->getRows()[0]->getMetricValues()[0]->getValue();
        });
    }

    public function getUsersTrendLast30Days(): array
    {
        return Cache::remember('ga_users_trend_last_30_days', 3600, function () {
            $response = $this->client->runReport(new RunReportRequest([
                'property' => $this->propertyId,
                'date_ranges' => [
                    new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today']),
                ],
                'metrics' => [new Metric(['name' => 'activeUsers'])],
                'dimensions' => [new Dimension(['name' => 'date'])],
            ]));

            return collect($response->getRows())->map(function ($row) {
                return [
                    'date' => $row->getDimensionValues()[0]->getValue(),
                    'users' => (int) $row->getMetricValues()[0]->getValue(),
                ];
            })->toArray();
        });
    }

    public function getViewsTrendLast30Days(): array
    {
        return Cache::remember('ga_views_trend_last_30_days', 3600, function () {
            $response = $this->client->runReport(new RunReportRequest([
                'property' => $this->propertyId,
                'date_ranges' => [
                    new DateRange(['start_date' => '30daysAgo', 'end_date' => 'today']),
                ],
                'metrics' => [new Metric(['name' => 'screenPageViews'])],
                'dimensions' => [new Dimension(['name' => 'date'])],
            ]));

            return collect($response->getRows())->map(function ($row) {
                return [
                    'date' => $row->getDimensionValues()[0]->getValue(),
                    'views' => (int) $row->getMetricValues()[0]->getValue(),
                ];
            })->toArray();
        });
    }
}
