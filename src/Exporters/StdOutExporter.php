<?php

namespace NHL\Exporters;

use NHL\Contracts\AbstractExporter;
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
class StdOutExporter extends AbstractExporter
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
            if (strlen($event->describe()) > 1) {
                $this->command->climate->out($event->describe());
            } else { // DEBUG -- If this happens something wasn't parsed properly
                var_dump($event);
            }
        }
    }

}