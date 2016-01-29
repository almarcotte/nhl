<?php

namespace NHL\Exporters;

use NHL\Contracts\Exporter;
use NHL\Contracts\VerboseOutput;
use NHL\Entities\Game;

/**
 * Class PlainText
 * Exports a game's data to a plain text file
 *
 * @package NHL\Exporters
 */
class PlainText implements Exporter
{
    use VerboseOutput;

    /** @var Game $game */
    private $game;

    /**
     * @param Game $game
     *
     * @return PlainText
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * @return bool
     */
    public function export()
    {
        foreach($this->game->getEvents() as $event) {
            $this->out($event->describe());
        }

        return true;
    }

}