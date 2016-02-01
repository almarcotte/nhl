<?php

namespace NHL\Contracts;

use NHL\Command;
use NHL\Entities\Game;

/**
 * Interface Exporter
 * All exports should implement this interface
 *
 *
 * @package NHL
 */
interface Exporter
{
    /**
     * Set the game
     *
     * @param Game $game
     */
    public function setGame(Game $game);

    /**
     * Export the data
     */
    public function export();
}