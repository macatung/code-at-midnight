<?php

namespace App\Console\Commands;

use App\Services\CashbackOrderSyncService;
use Illuminate\Console\Command;

class CashbackSyncOrdersCommand extends Command
{
    protected $signature = 'cashback:sync-orders {--start=} {--end=}';
    protected $description = 'Sync conversion reports and orders from Shopee Open Platform Affiliate API';

    public function handle(CashbackOrderSyncService $syncService): int
    {
        $this->info('Starting Shopee Cashback order synchronization...');
        $start = $this->option('start') ? (int) $this->option('start') : null;
        $end = $this->option('end') ? (int) $this->option('end') : null;

        $orders = $syncService->syncFromShopee($start, $end);
        $this->info(sprintf('Successfully processed %d orders.', count($orders)));

        return Command::SUCCESS;
    }
}
