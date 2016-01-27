<?php

namespace NHL\Events;

use NHL\Event;

/**
 * Class Penalty
 *
 * @package NHL\Events
 */
class Penalty extends Event
{
    const PLAYERS_REGEX = "/([A-Z]{3})(?:\\h{1}#)(\\d{1,2})(?:\\h{1})([A-Z \\-]+)/";
    const DETAULS_REGEX = "/(?:@)([A-Za-z]+)(?:\\((\\d+) min\\))(?:[, ]+)(?:([A-Za-z .]+)Drawn By: )/";

    /**
     * @inheritdoc
     */
    public function parse()
    {
        $data = $this->toArray();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        if (preg_match_all(self::PLAYERS_REGEX, $this->line, $players)) {
            var_dump($players);
        }
    }

    /**
     * @inheritdoc
     */
    public function describe()
    {
        return $this->line;
    }

}