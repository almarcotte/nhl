<?php

namespace NHL\Exporters;

use NHL\Contracts\Exporter;
use NHL\Contracts\VerboseOutput;
use NHL\Entities\Game;
use NHL\Exceptions\ExporterException;

/**
 * Class StdOut
 *
 * Prints the data to the standard output
 *
 * @package NHL\Exporters
 */
class StdOut extends Void implements Exporter
{
    use VerboseOutput;

    /** @var Game */
    protected $game;

    /**
     * @inheritdoc
     * @throws ExporterException
     */
    public function export()
    {
        foreach ($this->game->getEvents() as $event) {
            var_dump($event->line);
            $this->command->climate->out($event->describe());
        }
    }

}