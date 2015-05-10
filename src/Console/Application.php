<?php

namespace Herald\Console;

use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Herald\Console\Command\RunCommand;

/**
 * Herald Application
 */
class Application extends BaseApplication
{
    /**
     * {@inheritDoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        return 'herald:run';
    }

    /**
     * {@inheritDoc}
     */
    protected function getDefaultCommands()
    {
        $defaultCommands = parent::getDefaultCommands();

        $defaultCommands[] = new RunCommand();

        return $defaultCommands;
    }

    /**
     * {@inheritDoc}
     */
    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();

        $inputDefinition->setArguments();

        return $inputDefinition;
    }
}
