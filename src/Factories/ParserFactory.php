<?php

namespace NHL\Factories;

use NHL\Command;
use NHL\Contracts\AbstractParser;
use NHL\Parsers\PlayByPlayParser;

/**
 * Class ParserFactory
 *
 * @package NHL\Factories
 */
class ParserFactory
{
    /**
     * @param         $type
     * @param Command $command
     *
     * @return AbstractParser
     */
    public static function make($type, Command $command)
    {
        switch ($type) {
            case 'playbyplay':
            default:
                return new PlayByPlayParser($command);
        }
    }

}