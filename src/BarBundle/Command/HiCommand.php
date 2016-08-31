<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/30/16
 * Time: 8:47 PM
 *
 * Demo command
 *
 * @package  symfony-command-chaining
 * @author   johnleytondiaz <jdiaz@secureaudit.co>
 */
namespace BarBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use ChainCommandBundle\Interfaces\ChainInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HiCommand extends Command implements ChainInterface
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
        return ['foo:bar'];
    }

    /**
     * @return string
     */
    public static function getCommandName()
    {
        return 'bar:hi';
    }

    /**
     * Command output, returns an int for command STDERR
     *
     * @param OutputInterface $output
     * @return OutputInterface
     */
    public function commandOutput(OutputInterface $output)
    {
        $output->writeln('Hi from Bar!');

        return 1;
    }

    /**
     * Configures the command
     */
    protected function configure()
    {
        $this
            ->setName(self::getCommandName())
            ->setDescription('Demo from bar hi.');
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
