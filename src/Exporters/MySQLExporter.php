<?php

namespace NHL\Exporters;

use NHL\Command;
use NHL\Contracts\AbstractExporter;
use NHL\Entities\Game;

/**
 * Class MySQL
 *
 * @package NHL\Exporters
 */
class MySQLExporter extends AbstractExporter
{

    /** @var Game $game */
    protected $game;

    /**
     * @inheritdoc
     */
    public function export()
    {
    }
}