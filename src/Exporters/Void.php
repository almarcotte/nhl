<?php

namespace NHL\Exporters;


use NHL\Contracts\Exporter;

/**
 * Class Void
 *
 * This export does nothing.
 *
 * @package NHL\Exporters
 */
class Void extends StdOut implements Exporter
{
    /**
     * @inheritdoc
     */
    public function export()
    {
    }

}