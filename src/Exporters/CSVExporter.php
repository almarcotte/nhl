<?php
namespace NHL\Exporters;

use NHL\Entities\Game;
use NHL\Event;
use NHL\Exceptions\ExporterException;

/**
 * Class CSV
 * Exports data to a CSV file
 *
 * @package NHL\Exporters
 */
class CSVExporter extends FileExporter
{
    /** @var Game $game */
    protected $game;

    /** @var array $ignoredColumns */
    protected $ignoredColumns;

    /**
     * @throws ExporterException
     */
    protected function prepare()
    {
        if (is_null($this->ignoredColumns)) {
            $this->ignoredColumns = explode(',', $this->getOption('ignoreColumns'));
            $this->ignoredColumns += ["eventType", "parsed", "line", "eventPeriod"];
        }

        if (!$this->hasOption('path')) {
            throw new ExporterException("Couldn't find configuration for export path. Check config.ini");
        }
        $this->path = $this->getOption('path');
        $directoryPath = $this->getRealPathFromTemplate($this->getOption('folderStructure'));
        $this->path .= $directoryPath;

        if (!is_dir($this->path)) {
            mkdir($this->path, 0777, true);
        }
    }

    /**
     * @inheritdoc
     */
    public function export()
    {
        $this->prepare();

        foreach ($this->game->getEvents() as $event) {
            $filePath = $this->path.DIRECTORY_SEPARATOR.$this->getOption('nameFormat');
            $filePath = str_replace('%EVENTTYPE%', $event->getType(), $filePath);
            if (!file_exists($filePath)) {
                // First write the column headers
                file_put_contents($filePath, implode(',', $this->getHeadersForEvent($event))."\n", FILE_APPEND | LOCK_EX);
            }
            file_put_contents($filePath, $this->getEventAsLine($event)."\n", FILE_APPEND | LOCK_EX);
        }

        return true;
    }

    /**
     * @param Event $event
     *
     * @return array
     */
    protected function getHeadersForEvent($event)
    {
        return array_filter(array_keys(get_object_vars($event)), function ($field) {
            return !in_array($field, $this->ignoredColumns);
        });
    }

    /**
     * Returns all the important fields of an event object as a comma-separated line.
     * For certain fields that are arrays (like Assists) they get concatenated by ;
     *
     * @param Event $event
     *
     * @return string
     */
    protected function getEventAsLine($event)
    {
        $fields = array_filter(get_object_vars($event), function ($field) {
            return !in_array($field, $this->ignoredColumns);
        }, ARRAY_FILTER_USE_KEY);

        $columns = [];
        foreach ($fields as $key => $value) {
            if (is_array($value)) {
                $columns[] = implode(';', $value);
            } else {
                $columns[] = $value;
            }
        }

        return implode(',', $columns);
    }

}