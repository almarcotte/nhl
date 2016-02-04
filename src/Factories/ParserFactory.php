<?php

namespace NHL\Factories;

use NHL\Command;
use NHL\Contracts\Parser;
use NHL\Parsers\PlayByPlay;

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
     * @return Parser
     */
    public static function make($type, Command $command)
    {
        switch ($type) {
            case 'playbyplay':
            default:
                return new PlayByPlay($command);
        }
    }

}