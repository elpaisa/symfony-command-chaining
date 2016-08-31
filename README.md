symfony-command-chaining
============

A Symfony project created on August 31, 2016, 12:42 am.

This project chains commands using a central bundle, if the bundle is removed the commands will execute without chaining, all commands
can be decoupled from the central chaining bundle easily by removing the override registerCommands from each bundle:

```
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
```

That one however can be customized to receive parameters and do some validation to chaining or not, depending on the need


*To install:*
```
 bash install.sh
```

After installing a pre-commit hook will be added to git, this will execute on every commit, running linting and unit tests

*About Unit tests:*

Unit tests are run automatically on every commit, if tests pass, commit is allowed, otherwise canceled

*About Linting*

Linting is done via phpmd

*Testing the Application commands*

```
php app/console foo:bar
```