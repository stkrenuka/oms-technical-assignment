<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeRepository extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'make:repository {name}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new repository class';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $path = app_path("Repositories/{$name}.php");

        if (File::exists($path)) {
            $this->error('Repository already exists!');
            return Command::FAILURE;
        }

        File::ensureDirectoryExists(app_path('Repositories'));

        $stub = <<<PHP
<?php

namespace App\Repositories;

class {$name}
{
    //
}
PHP;

        File::put($path, $stub);

        $this->info("Repository created successfully: {$path}");

        return Command::SUCCESS;
    }
}
