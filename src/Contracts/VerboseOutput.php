<?php

namespace NHL\Contracts;

use NHL\Command;

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