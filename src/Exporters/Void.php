<?php

namespace NHL\Exporters;


use NHL\Contracts\Exporter;
use NHL\Entities\Game;

/**
 * Class Void
 *
 * This export does nothing.
 *
 * @package NHL\Exporters
 */
class Void implements Exporter
{
    /** @var Game $game */
    protected $game;

    /**
     * @inheritdoc
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }
    /**
     * @inheritdoc
     */
    public function export()
    {
    }

}