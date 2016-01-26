<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Shot
 * Represents a shot on goal event
 *
 * @package NHL\Events
 */
class Shot extends Event
{
    /**
     * REGEX to match a shot event line
     */
    const REGEX = "/([[:upper:]]+) ([[:upper:]]+) - #(\\d+) ([A-Z ]+), (\\w+), ([A-Za-z\\. ]+), (\\d+ ft.)/i";

    const DESCRIBE = "[P%s: %s] %s shot %s by #%s %s (%s) from %s (%s)";

    /**
     * @return string
     */
    public function getType()
    {
        return Types::SHOT;
    }

    /**
     * Parse a SHOT event line.
     * @return bool
     */
    public function parse()
    {
        // TOR ONGOAL - #21 VAN RIEMSDYK, Wrist, Off. Zone, 46 ft.
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->type = $data['type'];
        $this->location = $data['location'];
        $this->distance = $data['distance'];
        $this->team = new Team($data['team']);
        $this->target = $data['target'];
        $this->player = new Player($data['number'], $data['player'], $this->team);

        $this->parsed = true;

        return true;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'team' => $matches[1][0],
                'target' => $matches[2][0],
                'number' => $matches[3][0],
                'player' => $matches[4][0],
                'type' => $matches[5][0],
                'location' => $matches[6][0],
                'distance' => $matches[7][0]
            ];
        }
    }

    /**
     * Describes the shot in a human-readable format
     *
     * @return string
     */
    public function describe()
    {
        if ($this->parsed) {
            return sprintf(self::DESCRIBE,
                $this->period,
                $this->time,
                $this->type,
                $this->target,
                $this->player->number,
                $this->player->name,
                $this->team->name,
                $this->location,
                $this->distance
            );
        }
    }

}