<?php
namespace SilentRidge\Asap\Commands;

use Symfony\Component\Console\Input\InputOption;
use Illuminate\Console\Command;
use TitasGailius\Terminal\Terminal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Terminal as SymfonyTerminal;
use PHPFile;
use Ajthinking\LaravelFile;
use SilentRidge\Asap\Commands\BaseCommand;

class TestCommand extends BaseCommand
{

    protected $name = "asap:test";

    /**
     * Not implemented
     */
    protected $signature = 'asap:test - generate tests files based on current code base ';


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
