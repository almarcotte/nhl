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
    const REGEX = "/([A-Z]{3}) ([A-Z]+) - #(\\d+) ([A-Z\\h\\-]+), ([A-Za-z\\h\\-]+), ([A-Za-z\\. ]+), (\\d+) ft./";

    const DESCRIBE = "[P%s: %s] %s shot %s by %s from %s (%s ft.)";

    /** @var string $eventType */
    public $eventType = Types::SHOT;

    /** @var string $shotType */
    public $shotType;

    /** @var string $location */
    public $location;

    /** @var string $distance */
    public $distance;

    /** @var Team $team */
    public $team;

    /** @var string $target */
    public $target;

    /** @var Player $ */
    public $player;

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

        $this->shotType = $data['type'];
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
                $this->eventPeriod,
                $this->eventTime,
                $this->shotType,
                $this->target,
                $this->player,
                $this->location,
                $this->distance
            );
        }
    }

}