<?php
namespace NHL\Exporters;


use NHL\Command;
use NHL\Contracts\Exporter;
use NHL\Entities\Game;

/**
 * Class CSV
 * Exports data to a CSV file
 *
 * @package NHL\Exporters
 */
class CSV implements Exporter
{

    /** @var Game $game */
    private $game;

    /**
     * @param Game $game
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    public function export()
    {
        return true;
    }

}