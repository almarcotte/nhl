<?php

namespace NHL\Exporters;

use NHL\Contracts\Exporter;
use NHL\Contracts\VerboseOutput;
use NHL\Contracts\WithOptions;
use NHL\Entities\Game;
use NHL\Exceptions\ExporterException;

/**
 * Class PlainText
 * Exports a game's data to a plain text file
 *
 * @package NHL\Exporters
 */
class File implements Exporter
{
    use VerboseOutput;
    use WithOptions;

    /** @var Game $game */
    private $game;

    /**
     * @inheritdoc
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @return bool
     * @throws ExporterException
     */
    public function export()
    {
        if (!isset($this->options['exportPath']) || !is_writable($this->options['exportPath'])) {
            throw new ExporterException("Can't write to export path set in the options.");
        }

        foreach ($this->game->getEvents() as $event) {
            $this->out($event->describe());
        }

        return true;
    }

}