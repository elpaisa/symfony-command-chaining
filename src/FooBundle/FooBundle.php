<?php

namespace FooBundle;

use Symfony\Component\Console\Application;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class FooBundle extends Bundle
{
    /**
     * Lets let ChainCommandBundle to register commands in order to keep cmd chain,
     * if no chain is needed just remove this method
     *
     * @param Application $application
     * @return bool
     */
    public function registerCommands(Application $application)
    {
        return false;
    }
}
