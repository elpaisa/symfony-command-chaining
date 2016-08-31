<?php

namespace BarBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;


class BarBundle extends Bundle
{
    /**
     * Lets let ChainCommandBundle to register commands in order to keep cmd chain
     * 
     * @param Application $application
     * @return bool
     */
    public function registerCommands(Application $application)
    {
        return false;
    }
}
