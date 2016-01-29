<?php
namespace NHL\Exporters;


use NHL\Contracts\Exporter;

/**
 * Class CSV
 * Exports data to a CSV file
 *
 * @package NHL\Exporters
 */
class CSV implements Exporter
{

    public function export()
    {
        return true;
    }

}