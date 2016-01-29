<?php

namespace NHL\Contracts;

use NHL\Command;

/**
 * Class VerboseOutput
 * Allows an exporter to display data when verbose output is enabled
 *
 * @package NHL\Contracts
 */
trait VerboseOutput
{

    /** @var Command $command */
    protected $command;

    /**
     * @param Command $command
     */
    public function setCommand(Command $command)
    {
        $this->command = $command;
    }

    /**
     * @param string $message
     */
    public function out($message)
    {
        $this->command->out($message);
    }

}