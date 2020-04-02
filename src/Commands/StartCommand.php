<?php
/**
 * Created by IntelliJ IDEA.
 * User: hugh.li
 * Date: 2020/3/29
 * Time: 19:33.
 */

namespace HughCube\Laravel\Swoole\Commands;

use HughCube\Laravel\Swoole\Manager;
use Illuminate\Console\Command;

class StartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'swoole:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start swoole server';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function handle()
    {
        /** @var Manager $manager */
        $manager = $this->laravel->make(Manager::class);

        $manager->connection()->run();
    }
}
