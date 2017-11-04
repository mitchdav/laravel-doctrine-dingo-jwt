<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputArgument;

class ControllerMakeCommand extends GeneratorCommand
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $name = 'make:tempest-tools-controller {name} \'{repository}\' {namespace_root?}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $description = 'Create a new controller class that uses Tempest Tools Crud functionality';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected /** @noinspection ClassOverridesFieldOfSuperClassInspection */ $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub():string
    {
        return __DIR__ . '/stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace):string
    {
        $namespaceRoot = $this->argument('namespace_root');
        $namespaceRoot = $namespaceRoot??$rootNamespace . '\Controllers';
        return $namespaceRoot;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments():array
    {
        return [
            ['name', InputArgument::REQUIRED, 'Name of new controller'],
            ['repository', InputArgument::REQUIRED, 'Name of repository to use'],
            ['namespace_root', InputArgument::OPTIONAL, 'The root namespace where both the controller and the repo should be located']
        ];
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name):string
    {
        $namespace = $this->getNamespace($name);
        $namespace = preg_replace('/\\\Controllers$/', '', $namespace);
        /** @noinspection CallableParameterUseCaseInTypeContextInspection */
        $name = $this->argument('name');
        $repo = $this->argument('repository');

        $string = parent::buildClass($name);
        $string =  str_replace(array('NamespaceRoot', 'DummyRepo', 'DummyClass'), array($namespace, $repo, $name), $string);
        return $string;
    }
}
