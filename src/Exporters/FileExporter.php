<?php

namespace NHL\Exporters;

use NHL\Contracts\AbstractExporter;
use NHL\Contracts\VerboseOutput;
use NHL\Contracts\WithOptions;
use NHL\Entities\Game;
use NHL\Exceptions\ExporterException;

/**
 * Class File
 * Exports a game's data to a plain text file. This is exactly the same as the stdout exporter
 * but the content is written to a file.
 *
 * @package NHL\Exporters
 */
class FileExporter extends AbstractExporter
{
    use VerboseOutput;
    use WithOptions;

    /** @var string $path */
    protected $path;

    /** @var Game $game */
    protected $game;

    /**
     * @var array
     */
    protected $fileNameVariables = [
        '%GAMEID%' => 'id',
        '%SEASON%' => 'season',
        '%HOMETEAM%' => 'home',
        '%AWAYTEAM%' => 'away',
        '%SHORTID%' => 'shortID'
    ];

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

        $this->path .= DIRECTORY_SEPARATOR . $this->getRealPathFromTemplate($this->getOption('nameFormat'));

        if ($this->getOption('oneFilePergame') && file_exists($this->path)) {
            unlink($this->path);
        }
    }

    /**
     * @inheritdoc
     * @throws ExporterException
     */
    public function export()
    {
        $this->validate();

        foreach ($this->game->getEvents() as $event) {
            file_put_contents($this->path, $event->describe() . "\n", FILE_APPEND | LOCK_EX);
        }
    }

    /**
     * Creates a filename based on the template set in config.ini
     *
     * @param $template
     *
     * @return string
     */
    protected function getRealPathFromTemplate($template)
    {
        foreach($this->fileNameVariables as $placeholder => $field) {
            $template = str_replace($placeholder, $this->game->{$field}, $template);
        }

        if (!is_null($this->hasOption('extension'))) {
            $template .= $this->getOption('extension');
        }

        return $template;
    }

}