<?php

namespace App\Console\Commands;

use App\Support\MediaAssetLibrary;
use Illuminate\Console\Command;

class SyncMediaAssetsCommand extends Command
{
    protected $signature = 'media-assets:sync';

    protected $description = 'Add existing images on the public disk to the Assets library and remove entries whose file is missing';

    public function handle(): int
    {
        $result = MediaAssetLibrary::sync();

        $this->info("Added: {$result['added']}");
        $this->info("Removed (file missing): {$result['removed']}");

        return self::SUCCESS;
    }
}
