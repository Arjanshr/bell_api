<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Meilisearch\Client;

class ConfigureMeilisearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meilisearch:configure-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Configure Meilisearch index for products (filterable & sortable attributes)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $client = new Client(config('scout.meilisearch.host'), config('scout.meilisearch.key'));
        $index = $client->index('products');

        // Set filterable fields
        $index->updateFilterableAttributes([
            'status',
            'brand_id',
            'category_id',
        ]);

        // Set sortable fields
        $index->updateSortableAttributes([
            'price',
            'id',
            'created_at_timestamp',
            'rating',
        ]);

        $this->info('Meilisearch "products" index configured successfully.');
    }
}
