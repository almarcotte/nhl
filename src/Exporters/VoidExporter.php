<?php

namespace NHL\Exporters;

use NHL\Contracts\AbstractExporter;
use NHL\Entities\Game;

/**
 * Class Void
 *
 * This export does nothing.
 *
 * @package NHL\Exporters
 */
class VoidExporter extends AbstractExporter
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