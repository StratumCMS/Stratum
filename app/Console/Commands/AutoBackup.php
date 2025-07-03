<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class AutoBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:auto';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Effectuer une sauvegarde automatique si activée';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!setting('auto_backup')) {
            return;
        }

        $this->info('Sauvegarde automatique déclenchée…');

        Artisan::call('backup:run');
    }
}
