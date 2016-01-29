<?php

namespace NHL\Exporters;

use NHL\Contracts\Exporter;

/**
 * Class MySQL
 *
 * @package NHL\Exporters
 */
class MySQL implements Exporter
{
    public function export()
    {
        return true;
    }
}