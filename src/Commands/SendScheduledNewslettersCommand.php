<?php

namespace JeffersonGoncalves\Newsletter\Commands;

use Illuminate\Console\Command;
use JeffersonGoncalves\Newsletter\Jobs\SendScheduledNewslettersJob;

class SendScheduledNewslettersCommand extends Command
{
    protected $signature = 'newsletter:send-scheduled';

    protected $description = 'Dispatch the job that sends every due scheduled newsletter';

    public function handle(): int
    {
        SendScheduledNewslettersJob::dispatch();

        $this->info('Scheduled newsletters dispatch job queued.');

        return self::SUCCESS;
    }
}
