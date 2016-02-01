<?php
namespace NHL\Exporters;


use NHL\Contracts\Exporter;
use NHL\Contracts\WithOptions;
use NHL\Entities\Game;

/**
 * Class CSV
 * Exports data to a CSV file
 *
 * @package NHL\Exporters
 */
class CSV extends File implements Exporter
{

    /** @var Game $game */
    private $game;

    /**
     * @inheritdoc
     */
    public function export()
    {
        return true;
    }

}