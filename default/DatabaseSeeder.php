<?php

namespace Database\Seeders;
use PHPFile;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $finder = new Finder();
        $files = $finder
            ->files()
            ->in(database_path("seeders"))->filter(function($name) {
                return !Str::contains($name, "DatabaseSeeder");
            });

            foreach($files as $file)
            {

                $parts = explode('\\', $file);

                $className = basename($parts[ count($parts) - 1]);
                $className = trim(preg_replace("/.php/", "", $className));
                $seeder = $this->container->make("\\" . __NAMESPACE__ . "\\" . $className);
                $seeder->run();
            }

        try {
            $user = User::factory([
                    'name' => Str::random(10),
                    'email' => 'info@silentridge.io',
                    'password' => Hash::make('asdasd')
                ])->create();

        } catch(\Exception $e){
            $this->command->getOutput()->writeln("exception". print_r($e->getMessage(),1));
        }

    }
}
