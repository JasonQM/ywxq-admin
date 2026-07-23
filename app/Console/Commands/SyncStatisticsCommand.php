<?php

namespace App\Console\Commands;

use App\Services\GameStatisticsSyncService;
use Illuminate\Console\Command;

class SyncStatisticsCommand extends Command
{
    protected $signature = 'game:sync-stats {--start=} {--end=}';

    protected $description = 'Sync game daily statistics and real-name counts from remote APIs.';

    public function handle(GameStatisticsSyncService $service): int
    {
        $result = $service->syncAll($this->option('start'), $this->option('end'));

        $this->info(sprintf(
            '统计更新 %d 条，实名更新 %d 条，生成预警 %d 条。',
            $result['statsUpdated'],
            $result['realNameUpdated'],
            $result['alerts'],
        ));

        return self::SUCCESS;
    }
}
