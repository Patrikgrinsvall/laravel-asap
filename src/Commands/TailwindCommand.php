<?php
namespace SilentRidge\Asap\Commands;

use Symfony\Component\Console\Input\InputOption;
use TitasGailius\Terminal\Terminal;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use SilentRidge\Asap\Commands\BaseCommand;

class TailwindCommand extends BaseCommand
{

    protected $name = "asap:tailwind";

    /**
     * Not implemented
     */
    protected $signature = 'asap:tailwind - create tailwindcss configuration randomly, that doesnt look to bad';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'not implamented';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        return 0;
    }


}
