<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Storage;
use LaravelZero\Framework\Commands\Command;

/**
 * Class Connect
 * @package App\Commands
 */
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
    protected $description = 'fast vnc connection for testing with selenium nodes (auto-range)';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $defaultPort = 32768;

        #arguments
        $host = $this->argument('host');
        $nodes = $this->argument('nodes');

        #options
        $startPort = $this->option('startPort');
        $password = $this->option('password'); // md5 here...
        $autoStart = $this->option('autoStart');

        if (!empty($startPort)) {
            $defaultPort = $startPort;
        }

        if (empty($nodes)){
            $this->error('please enter a valid number of connections....');
            return false;
        }

        if (!empty($password)){
            $this->error('password not working, skyping....');
        }

        $endPort = $defaultPort + ($nodes -1);
        $this->warn("connection range [$defaultPort - $endPort]....");

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
                $this->warn("opening connection in port $defaultPort....");
                $script .= "xdg-open storage/$fileName ; ";
            }

            $defaultPort ++; // increase
        }

        exec($script);

        //clean tmp connections - comments in test
        exec("rm -rf storage/$host");

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
