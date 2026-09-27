<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessWargaData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-warga-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Mulai memproses data...');

        Warga::chunk(100, function ($wargas) {
            foreach ($wargas as $warga) {
            }
        });

        $this->info('Selesai memproses data.');
    }
}

