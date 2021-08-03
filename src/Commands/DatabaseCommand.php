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

/**
 * This class is runned from artisan and is used to run a series of commands to create a laravel + nova website from existing database.
 */
class DatabaseCommand extends BaseCommand
{
    protected $name = "asap:database";
    public $databased = array();
    /**
     * The name and signature of the console command.
     *
     * --skip-composer  -   Skip running `composer dump` before running this command. This is only to be used in development of the command since it speeds up things.
     *                      When used to database site it shouldnt be used since classnames are cached and needs to be updated between runs.
     *
     * --overwrite      -   This will send in --force or --overwrite or corresponding to the commands.
     *                      This might overwrite work if the site is already built but just needs a few more columns or similar.
     *
     * --ignore         -   Probably the most useful option. This will ignore database tables from all generators.
     *
     * @var string
     */
    protected $signature = 'asap:database';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates database related parts of a site based on current selected database. Does not overwrite but places files in base folder in each directry';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

/**
     * Default ignorelist.
     * This can be tables, models, migrations or factories, it matches all both in singular and plural.
     *
     * @return void
     */
    public function initIgnoreList()
    {
        $config = Config::all();

        // these are from relise
        if (isset($config['models'])) {
            $this->ignore($config['models']['*']['except']);
        }

        if(isset($config['nova-generator']['ignore']))
        {
            $this->ignore($config['nova-generator']['ignore']);
        }

    }
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->initIgnoreList();
        $this->initializeDatabase();
        try {
            $this->printRGB('Installing ', 'Migrations');
            $this->call('migrate:install', ['--no-interaction' => true, '--quiet' => true]);
        }catch(\Exception $e) {
            $this->printRGB("exception", $e->getMessage());
        }
        $this->printRGB('Publishing ', 'Dependency', 'Assets');
        $this->call('vendor:publish', [
            '--tag' => [
                'nova-generator',
                'nova-csv-import',
                'nova-migrations',
                'nova-provider',
                'artisan-tool',
                'config',
                'migrations',
                'nova-button',
                'tailwind-public-css',
                'stubs'
            ]
        ]);

        $this->printRGB('Fixing ', 'Default', 'Stub');
        $this->defaultSeeder();


        $this->resources = $this->getArrayWithResources();

        $this->resources->each(function($table){
            if($this->option('overwrite') !== null) {
                // $this->purgeMigrations($table); // not reliable
            }
            $this->printRGB("Generating", "Migration", $table['table']);
            $migrationCommand = 'migrate:generate';

            $migrationOptions = ['--tables' => $table['table'], '--no-interaction' => true, '--quiet' => true];
            $return = $this->call($migrationCommand, $migrationOptions);

            // also insert
            //DB::insert('insert into migrations (migration, batch) values(?,?)', [1, 1]);
            if($return == 0) {
                array_push($this->generated, $table['table']);
            }
        });

        $this->printRGB("Creating", "Models");
        $this->call('code:model');
        $models = rtrim($this->resources->implode('model_name', ","),",");
        if($this->option('overwrite') === null) {
            $this->printRGB("Force", "Generating factories");
            $this->call('generate:factory', [$models] );
        }
        else {
            $this->printRGB("Generating factories");
            $this->call('generate:factory', [$models, '--force' => 'true'] );
        }
        $this->generateSeeders();
        $this->call('generate:nova');

        //$this->call('migrate:fresh', ['--seed' => 'true']);
        /*foreach($cleanFilelist as $file) {
            $this->call('migrate', ['--seed' => 'true']);
        }*/
        if($this->option('skip-composer') === null)
        {
            $this->printRGB("Updating", "Dependencies");
            Terminal::with([
                'cmd' => "composer",
                'options' => "dump",
            ])->run('{{ $cmd }} {{ $options }}');

        }
        $this->printRGB("Cleaning ", "Artisan Cache");
        $this->call('cache:clear');
        return 0;
    }

    /**
     * Get all tables/models/etc excepted from writing
     * @return Collection
     */
    /*
    function getFileList()
    {
        $files = scandir(app_path("Models"));

        $filelist = [];
        foreach($files as $file)
        {
            if(in_array($file,[".","..",".gitignore"])) continue;
            $modelname = explode(".", $file);
            if($this->ignored($modelname)) {continue; $this->printRGB("Skipping", $file, "since it was found in except list in config or invalid");}

            $outfile['file'] = $file;
            $outfile['model_file'] = app_path("Models/$file");
            $modelname = $modelname[0];
            $outfile['model_name'] = $modelname;
            $modelfile = app_path("Models/" . $modelname . ".php");
            if($this->modelHasFactory($modelfile)) $outfile['hasFactory'] = true;
            else $outfile['hasFactory'] = false;
            $filelist[] = $outfile;
        }
        return collect($filelist);
    }*/

    /**
     * True if trait hasFactory exists in model
     *
     * @param string $filename
     * @return bool
     */
    function modelHasFactory(String $filename) : bool
    {
        if(file_exists($filename) && !stripos(file_get_contents($filename), "hasFactory")) return false;
        else return true;
    }

    /**
     *  Copy default seeder if it doesnt exists
     *
     * @return void
     */
    public function defaultSeeder()
    {
        if($this->finder
            ->in(base_path("database/seeders/"))
            ->name("DatabaseSeeder.php")
            ->count() == 0)
        {
            $this->printRGB("Missing", "DatabaseSeeder", "Copying original one");
            copy(__DIR__ . "/../../default/DatabaseSeeder.php", base_path("database/seeders/") . "DatabaseSeeder.php");
        }
        if($this->finder->in(base_path('stubs'))->count()==0) {
            $this->printRGB('Creating', 'Stubs', "directory");
            mkdir(base_path("stubs"));
        }
        copy(__DIR__ . "/../../default/seeder.stub", base_path("stubs/seeder.stub"));
    }

    /**
     * Generate seeders for all tables
     * @return void
     */
    function generateSeeders()
    {

        $models = $this->resources;
        foreach($models as $model)
        {

            if(!$model['hasFactory']) continue;
            $dirtySeederFilename    = base_path("database/seeders/" . $model['model_name'] . ".php");
            $seederFilename         = base_path("database/seeders/" . $model['model_name'] . "Seeder.php");
            if($this->option('overwrite')!==null && file_exists($seederFilename)) unlink($seederFilename);
            $this->call('make:seeder', ["name" => $model['model_name']]);

            $this->printRGB("Copying dirty seederfile", $dirtySeederFilename, "to", $seederFilename);
            rename($dirtySeederFilename, $seederFilename);
        }
    }

    public function init()
    {
        $this->ignore([
            'User',
            /*'ActionEvent',
            'action_events',
            'CreateWidgetsTable',
            'BoardFilter',
            'BoardStandard',
            'BoardWidget',
            'Board',
            'FilterStandard',
            'Filter',
            'MetricStandard',
            'VisualStandard',
            'WidgetConfiguration',
            'Widget'*/
        ]);

        $config = Config::all();;
        $exceptModels = $config['models']['*']['except'] ?? [];
        $this->ignore($exceptModels);

        if(isset($config['nova-generator']['except'])) {
            $this->ignore($config['nova-generator']['except']);
        }

    }
}
