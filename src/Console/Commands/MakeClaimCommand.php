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
        return __DIR__ . '/../../stubs/claim.stub';
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        $config = config('passport-meta-claim');

        $path = str_replace(app_path(), '', $config['path']);
        
        return $rootNamespace . str_replace('/', '\\', $path);
    }

    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);
        
        return str_replace(
            'DummySuffix',
            config('passport-meta-claim.suffix'),
            $stub
        );
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
}
