<?php
/**
 * Created by IntelliJ IDEA.
 * User: johnleytondiaz
 * Date: 8/31/16
 * Time: 12:04 PM
 *
 * Demo bundle to chain commands
 *
 * @package  symfony-command-chaining
 * @author   johnleytondiaz <jdiaz@secureaudit.co>
 */
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
