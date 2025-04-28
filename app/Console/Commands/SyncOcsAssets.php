<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\OcsInventoryService;

class SyncOcsAssets extends Command
{
    protected $signature = 'assets:sync';
    protected $description = 'Synchronizes assets from OCS Inventory';

    protected $ocsService;

    public function __construct(OcsInventoryService $ocsService)
    {
        parent::__construct();
        $this->ocsService = $ocsService;
    }

    public function handle()
    {
        $this->info('Starting asset synchronization...');
        
        try {
            $newAssets = $this->ocsService->syncAssets();
            $this->info("Synchronization completed. {$newAssets} new assets imported.");
        } catch (\Exception $e) {
            $this->error('Error during synchronization: ' . $e->getMessage());
        }
        
        return 0;
    }
}