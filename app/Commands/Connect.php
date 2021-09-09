<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

class Connect extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'connect:range {host} {nodes} {--startPort=} {--password} {--autoStart}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Fast vnc connection for testing with selenium nodes (auto-range)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $defaultPort = 32768;

        $host = $this->argument('host');
        $nodes = $this->argument('nodes');

        $startPort = $this->option('startPort');
        $password = $this->option('password');
        $autoStart = $this->option('autoStart');

        if (!empty($startPort)) {
            $defaultPort = $startPort;
        }

        if (is_int($nodes)){
            //$this->error('Please enter a valid number of connections.');
        }

        $script = "";
        for ($i = 1; $i <= $nodes; $i++) {
            $fileName = sprintf(
                '%s/%s:%s.vnc',
                $host,
                $host,
                $defaultPort
            );

            $content = "ConnMethod=tcp\n
                ConnTime=2021-09-09T18:09:26.017Z\n
                FriendlyName=$host:$defaultPort\n
                Host=$host:$defaultPort\n
                Password=2e2dbf576eb06c9e\n
                RelativePtr=0\n
                Uuid=82c9e5c3-f71d-4b70-8f9a-b88d3b5829fc";
            Storage::put($fileName, $content);

            if (!empty($autoStart)) {
                $this->warn("Abrindo conexão $defaultPort");
                $script .= "xdg-open storage/$fileName ; ";
            }

            $defaultPort ++; // increase
        }

        exec($script);

        $this->info("foram criados [$nodes] arquivos de conexão...");

        return  true;
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
