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
    public function setGame(Game $game);
    public function export();
}