<?php

namespace NHL\Contracts;

use NHL\Entities\Game;

/**
 * Interface AbstractExporter
 * All exports should implement this interface
 *
 *
 * @package NHL
 */
abstract class AbstractExporter
{
    /**
     * Set the game
     *
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Export the data
     */
    abstract public function export();
}