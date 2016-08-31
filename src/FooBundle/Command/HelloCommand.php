<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/31/16
 * Time: 12:04 PM
 *
 * Demo command
 *
 * @package  symfony-command-chaining
 * @author   johnleytondiaz <jdiaz@secureaudit.co>
 */
namespace FooBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use ChainCommandBundle\Interfaces\ChainInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends ContainerAwareCommand implements ChainInterface
{
    /**
     * @var ChainCommandBundle
     */
    public $chainingBundle;

    /**
     * @return array
     */
    public static function depends()
    {
        return [];
    }

    /**
     * @return string
     */
    public static function getCommandName()
    {
        return 'foo:bar';
    }

    /**
     * Command output, returns an int for command STDERR 
     *
     * @param OutputInterface $output
     * @return int
     */
    public function commandOutput(OutputInterface $output)
    {
        $output->writeln('Hello from Foo!');

        return 1;
    }


    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName(self::getCommandName())
            ->setDescription('Demo from hello.');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->chainingBundle instanceof \ChainCommandBundle\ChainCommandBundle) {
            return $this->chainingBundle->execute($this->getName(), $output, array($this, 'commandOutput'));
        }

        return $this->commandOutput($output);
    }

}
