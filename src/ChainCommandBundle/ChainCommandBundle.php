<?php

namespace ChainCommandBundle;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Application;
use Carbon\Carbon;
use Symfony\Component\Console\Output\OutputInterface;

class ChainCommandBundle extends Bundle
{
    /**
     * @var array
     */
    public $chain = [];

    /**
     * @var array
     */
    public $trackThese = [];

    /**
     * @var LoggerInterface
     */
    private $_logger;

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->_logger) {
            $this->_logger = $this->container->get('logger');
        }

        return $this->_logger;
    }

    /**
     * Registers a command as a member of another in the chain
     *
     * @param string $chain
     * @param string $member
     * @throws \Exception
     */
    public function registerAsMemberOf(string $chain, string $member)
    {
        if (isset($this->chain[$chain]) && in_array($member, $this->chain[$chain])) {
            throw new \Exception("Duplicate member attempt for $chain => $member");
        }

        $this->getLogger()->info("$chain is a master command of a command chain that has registered member commands");
        $this->getLogger()->info("$member registered as a member of $chain command chain");

        $this->chain[$chain]   = $this->chain[$chain] ?? [];
        $this->chain[$chain][] = $member;
    }

    /**
     * Register dependent commands for the current one
     *
     * @param Command $class
     */
    private function registerDependencies($class)
    {
        $dependencies = $class::depends();
        $member       = $class::getCommandName();

        $this->trackThese[$member] = $class;

        foreach ($dependencies as $dep) {
            $this->registerAsMemberOf($dep, $member);
        }
    }

    /**
     * Finds the current command in the chain
     *
     * @param string $command
     * @return array|bool
     */
    public function isChild($command)
    {
        $parent = [];

        foreach ($this->chain as $key => $value) {
            if (array_search($command, $value) !== false) {
                $parent[] = $key;
            }
        }

        return count($parent) > 0 ? $parent : false;
    }

    /**
     * Executes the chained commands
     *
     * @param string          $command
     * @param OutputInterface $output
     * @param callable        $callback
     * @return int
     * @throws \Exception
     */
    public function execute(string $command, OutputInterface $output, $callback)
    {
        $isChild = $this->isChild($command);

        if ($isChild) {
            throw new \Exception(
                "Error: $command command is a member of " . implode(",",
                    $isChild) . " command chain and cannot be executed on its own."
            );
        }
        $this->getLogger()->info("Executing $command command itself first:");

        $call = $callback($output);
        $this->runDependencies($command);

        $this->getLogger()->info("Execution of $command chain completed");

        return (int)$call;
    }

    /**
     * Executes the main command and its chain, if some command is attached to a chain
     * and is executed directly, an exception will be thrown, no child commands can be
     * executed directly
     *
     * @param Command $command
     */
    private function runDependencies($command)
    {
        if (!isset($this->chain[$command]) || !is_array($this->chain[$command])) {
            return;
        }


        foreach ($this->chain[$command] as $chain) {
            $this->trackThese[$chain]
                ->commandOutput(new ConsoleOutput());
        }

    }

    /**
     * Finds and registers Commands, this methods overrides the parent
     * method to add command dependency registering, and looks for all bundle
     * commands in the parent directory, has the job of loading all commands
     * and its chain, if this method is removed all the commands will be loaded
     * by their respective bundle classes and will execute as a single command
     * with no chain
     *
     * @param Application $application An Application instance
     */
    public function registerCommands(Application $application)
    {

        if (!class_exists('Symfony\Component\Finder\Finder')) {
            throw new \RuntimeException('You need the symfony/finder component to register bundle commands.');
        }

        $finder = new Finder();
        $finder->files()->name('*Command.php')->in($this->getParentDir());

        foreach ($finder as $file) {

            $class = str_replace("/", "\\", $file->getRelativePath()) . "\\" . $file->getBasename('.php');

            if ($this->container) {
                $alias = 'console.command.' . strtolower(str_replace('\\', '_', $class));
                if ($this->container->has($alias)) {
                    continue;
                }
            }
            $newInstance = new $class();
            $this->registerDependencies($newInstance);
            $newInstance->chainingBundle = $this;
            $application->add($newInstance);
        }

    }

    /**
     * Gets the parent directory of the current bundle
     *
     * @return string
     */
    public function getParentDir()
    {
        return str_replace(basename(__DIR__), "", __DIR__);
    }

}
