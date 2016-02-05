<?php

namespace NHL\Factories;

use NHL\Command;
use NHL\Contracts\AbstractExporter;
use NHL\Exporters\CSVExporter;
use NHL\Exporters\FileExporter;
use NHL\Exporters\MySQLExporter;
use NHL\Exporters\StdOutExporter;
use NHL\Exporters\VoidExporter;

/**
 * Class ExporterFactory
 *
 * @package NHL
 */
class ExporterFactory
{
    /**
     * @param string  $exporter
     * @param Command $command
     *
     * @return AbstractExporter
     */
    public static function make($exporter, Command $command)
    {
        switch ($exporter) {
            case 'csv':
                $obj = new CSVExporter();
                break;
            case 'file':
                $obj = new FileExporter();
                break;
            case 'mysql':
                $obj = new MySQLExporter();
                break;
            case 'void':
                $obj = new VoidExporter();
                break;
            case 'stdout':
            default:
                $exporter = 'stdout';
                $obj = new StdOutExporter();
        }

        if (method_exists($obj, 'setOptions')) {
            $obj->setOptions($command->config->get($exporter));
            $obj->setOption('path', $command->config->get('export', 'path'));
        }

        if (method_exists($obj, 'setCommand')) {
            $obj->setCommand($command);
        }

        return $obj;
    }
}