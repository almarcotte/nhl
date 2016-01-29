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
class StdOut implements Exporter
{
    use VerboseOutput;

    /** @var Game $game */
    private $game;

    /**
     * @inheritdoc
     *
     * @return StdOut
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @inheritdoc
     * @throws ExporterException
     */
    public function export()
    {
        foreach ($this->game->getEvents() as $event) {
            $this->command->climate->out($event->describe());
        }
    }

}