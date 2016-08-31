<?php

/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/30/16
 * Time: 10:34 PM
 */
namespace ChainCommandBundle\Interfaces;

use Symfony\Component\Console\Output\OutputInterface;

interface ChainInterface
{
    public static function depends();

    public static function getCommandName();
    
    public function commandOutput(OutputInterface $output);
}