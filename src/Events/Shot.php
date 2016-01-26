<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

class Shot extends Event
{
    /** @var Player $player */
    public $player;

    /** @var Team $team */
    public $team;

    /** @var string $target */
    public $target;

    /** @var string $type */
    public $type;

    /** @var string $location */
    public $location;

    /** @var string $distance */
    public $distance;

    const REGEX = "/([[:upper:]]+) ([[:upper:]]+) - (#\\d+) ([A-Z ]+), (\\w+), ([A-Za-z\\. ]+), (\\d+ ft.)/i";

    /**
     * @return string
     */
    public function getType()
    {
        return Types::SHOT;
    }

    /**
     * Parse a SHOT event line.
     *
     * @param $line
     * @return mixed|void
     */
    public function parseLine($line)
    {
        // TOR ONGOAL - #21 VAN RIEMSDYK, Wrist, Off. Zone, 46 ft.
        $data = $this->toArray($line);

        $this->type = $data['type'];
        $this->location = $data['location'];
        $this->distance = $data['distance'];
        $this->team = new Team($data['team']);
        $this->player = new Player($data['number'], $data['player'], $this->team);
    }

    /**
     * @param $line
     * @return array
     */
    public function toArray($line)
    {
        preg_match_all(self::REGEX, $line, $matches);
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

    /**
     * Describes the shot in a human-readable format
     *
     * @return string
     */
    public function describe()
    {
        return $this->type . " shot " . $this->target . " by " . $this->player->getName() . "(".$this->team->getName().")"
            . " from " . $this->location . "(".$this->distance.")";
    }

}