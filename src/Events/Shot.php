<?php

namespace NHL\Events;

use NHL\Event;

class Shot extends Event
{
    public function getType()
    {
        return Types::SHOT;
    }

    /**
     * @param $line
     */
    public function parseLine($line)
    {
        // TOR ONGOAL - #21 VAN RIEMSDYK,
        // Wrist
        // Off. Zone
        // 46 ft.
        $exploded = explode(', ', $line);
        $playerAndTeam = str

    }

}