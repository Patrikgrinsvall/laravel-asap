<?php
namespace SilentRidge\Asap\Commands;

use SilentRidge\Asap\Traits\ColorfulCommands;
use Symfony\Component\Finder\Finder;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class BaseCommand extends Command
{
    use ColorfulCommands;

    public Collection $ignoreList;
    public $connection, $schemaManager, $symfonyTerminal, $finder;
	/**
	 * @var \Illuminate\Database\Migrations\MigrationRepositoryInterface  $repository
	 */
	protected $repository;
    public function __construct($name = null){
        parent::__construct();
        $this->finder = new Finder();
    }



    /**
     * Add resource to ignorelist
     *
     * @param array|null $ignored
     * @return void
     */
    public function ignore(mixed $ignored = null) : void
    {
        if (!isset($this->ignoreList))
            $this->ignoreList = new Collection();
        if ($ignored === null) {
            return;
        }

        if(is_string($ignored)) {
            $ignored = Str::lower($ignored);
            $this->ignoreList->push(Str::lower($ignored));// = Arr::crossJoin($ignored, $this->ignoreList);
        }


        if(is_array($ignored) && Arr::accessible($ignored)) {
            $ignore = collect($ignored);
            /*$ignore = $ignore->map(function($item, $key){
                return Str::lower($item);
            });*/
            $this->ignoreList = $this->ignoreList->merge($ignore);//$this->ignoreList = Arr::crossJoin($ignored, $this->ignoreList);
        }
    }

    /**
     *  Is resource in internal ignore list
     * @param string $name
     * @return bool
     */
    public function ignored(string $name) {

        if(Arr::has($this->ignoreList, Str::lower($name))) return true;
        if(Arr::has($this->ignoreList, Str::plural($name))) return true;
        if(Arr::has($this->ignoreList, Str::singular($name))) return true;
        if(Arr::has($this->ignoreList, Str::camel($name))) return true;
        if(Arr::has($this->ignoreList, Str::studly($name))) return true;

        return false;
    }

    /**
     * Setup database related stuff
     *
     * @return void
     */
    public function initializeDatabase(string $connectionName = "default")
    {
        $this->connection = Config::get('database.' . $connectionName);
        $this->printRGB('Using', $connectionName, $this->connection);
        $this->schemaManager = DB::connection($this->connection)->getDoctrineConnection()->getSchemaManager();
    }

    /**
     * Remove migrations for table name.
     * This is because we get errors if we have multiple migration files with same name.
     */
    function purgeMigrations($table) : void
    {
        /*
        PHPFile::in('database/migrations')
        ->where('classExtends', 'Migration')
        ->get()
        ->each(function($file) {
            echo $file->className();
        });
*/

        $table = Str::snake($table['table']);
        $allPaths = $this->finder
            ->files()
            ->in(database_path("migrations"))
            ->name('/create_' . $table . '/');
        if (!$allPaths->hasResults()) {
            $this->printRGB("Didnt find any migrations for table", $table);
            return;
        }
        foreach($allPaths as $path) {
            $this->printRGB("Deleting migration ", $path->getRealPath()," for table $table ");
            unlink($path->getRealPath());
        }
    }

    /**
     * Get a array of strings for models, files etc. to work with in the rest of the functions.
     *
     * @param mixed $selected - if provided only use these resource names and match against ignorelist
     * @return Collection
     */
    public function getArrayWithResources(Array $selected = null) : Collection
    {
        $resources = collect($this->schemaManager->listTableNames())
            ->reject(function(string $table) {
                return $this->ignoreList->contains($table);
            })
            ->filter(function(string $table) use($selected) {
                if(isset($selected) && !in_array($table, $selected)) return false;
                return true;
            })
            ->map(function (string $table)  {
                $model = Str::singular(ucwords(Str::camel($table)));
                $modelFile = app_path("Models/$model");
                $out['model_file'] = app_path("Models/$model");
                $out['model_filename'] = app_path("Models/$model.php");
                $out['model_name'] = $model;
                $out['hasFactory'] = $this->modelHasFactory($modelFile);
                $out['table'] = $table;
                return $out;
            });

        return $resources;
    }

    function logMigration($file, $batch) {

        $this->repository = new MigrationRepositoryInterface();
        $this->repository->setSource( $this->option( 'connection' ) );
        if ( ! $this->repository->repositoryExists() ) {
            $options = array('--database' => $this->option( 'connection' ) );
            $this->call('migrate:install', $options);
        }
        $batch = $this->repository->getNextBatchNumber();
        $this->repository->log($file, $batch);
    }
}
