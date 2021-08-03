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

class FrontendCommand extends BaseCommand
{

    protected $name = "asap:frontend";

    /**
     * Not implemented
     */
    protected $signature = 'asap:frontend - create frontend, views, forms, layouts, tailwind config (random or preset) etc. ';


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
