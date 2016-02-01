<?php

namespace NHL\Exporters;

use NHL\Contracts\Exporter;
use NHL\Contracts\VerboseOutput;
use NHL\Contracts\WithOptions;
use NHL\Entities\Game;
use NHL\Exceptions\ExporterException;

/**
 * Class PlainText
 * Exports a game's data to a plain text file. This is exactly the same as the stdout exporter
 * but the content is written to a file.
 *
 * @package NHL\Exporters
 */
class File implements Exporter
{
    use VerboseOutput;
    use WithOptions;

    /** @var string $path */
    protected $path;

    /** @var Game $game */
    private $game;

    /**
     * @var array
     */
    protected $fileNameVariables = [
        '%GAMEID%' => 'id',
        '%SEASON%' => 'season',
        '%HOMETEAM%' => 'home',
        '%AWAYTEAM' => 'away'
    ];

    /**
     * @inheritdoc
     */
    public function setGame(Game $game)
    {
        $this->game = $game;
    }

    /**
     * Validates the configuration and sets a few things up before exporting
     *
     * @throws ExporterException
     */
    protected function validate()
    {
        if (!$this->hasOption('path')) {
            throw new ExporterException("Couldn't find configuration for export path. Check config.ini");
        }
        $this->path = $this->getOption('path');

        if ($this->getOption('bySeason')) {
            // Putting each season in its own folder, make sure it exists
            $this->path = $this->path . DIRECTORY_SEPARATOR . $this->game->season;
            if (!is_dir($this->path)) {
                mkdir($this->path);
            }
        }

        $this->path .= DIRECTORY_SEPARATOR . $this->getFileNameFromTemplate();

        if ($this->getOption('oneFilePergame') && file_exists($this->path)) {
            unlink($this->path);
        }
    }

    /**
     * @return bool
     * @throws ExporterException
     */
    public function export()
    {
        $this->validate();

        foreach ($this->game->getEvents() as $event) {
            file_put_contents($this->path, $event->describe() . "\n", FILE_APPEND | LOCK_EX);
        }

        return true;
    }

    /**
     * Creates a filename based on the template set in config.ini
     *
     * @return string
     */
    protected function getFileNameFromTemplate()
    {
        $template = $this->getOption('nameFormat');
        foreach($this->fileNameVariables as $placeholder => $field) {
            $template = str_replace($placeholder, $this->game->{$field}, $template);
        }

        if (!is_null($this->hasOption('extension'))) {
            $template .= $this->getOption('extension');
        }

        return $template;
    }

}