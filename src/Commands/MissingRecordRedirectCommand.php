<?php

namespace Padmission\MissingRecordRedirect\Commands;

use Illuminate\Console\Command;

class MissingRecordRedirectCommand extends Command
{
    public $signature = 'missing-record-redirect';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
