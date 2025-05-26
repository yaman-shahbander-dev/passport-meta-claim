<?php

namespace PassportMetaClaim\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeClaimCommand extends GeneratorCommand
{
    protected $name = 'make:claim';

    protected $description = 'Create a new JWT claim class in the specified directory';

    protected $type = 'Claim';

    protected function getStub()
    {
        return __DIR__ . '/../../Stubs/claim.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $config = config('passport-meta-claim');

        $path = str_replace(app_path(), '', $config['path']);
        
        return $rootNamespace . str_replace('/', '\\', $path);
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Create the class even if the claim already exists'],
        ];
    }

    protected function qualifyClass($name)
    {
        $suffix = config('passport-meta-claim.suffix');
        
        if (! Str::endsWith($name, $suffix)) {
            $name .= $suffix;
        }

        return parent::qualifyClass($name);
    }

    public function handle()
    {
        $name = $this->argument('name');
        
        $suffix = config('passport-meta-claim.suffix');

        // Prevent the user from including the suffix in the input
        if (Str::endsWith($name, $suffix)) {
            $this->error("The claim name should not include the suffix '{$suffix}'.");

            return 1; 
        }

        return parent::handle();
    }
}
