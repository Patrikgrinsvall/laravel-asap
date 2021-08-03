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

class SeederCommand extends BaseCommand
{

    protected $name = "asap:seeder";

    /**
     * Not implemented
     */
    protected $signature = 'asap:seeder - create seeders that run factories but also with support for relations(WIP)';


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
