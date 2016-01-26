<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

class Miss extends Event
{
    protected $shotTypes = [
        'Wrist',
        'Snap',
        'Backhand',
        'Tip-In',
        'Slap'
    ];

    const REGEX = "/([[:upper:]]+) #(\\d+) ([A-Z ]+), (\\w+), ([A-Za-z\\. ]+), ([A-Za-z\\. ]+), (\\d+ ft.)/i";

    const DESCRIBE = "[P%s: %s] Missed %s shot by #%s %s (%s) from %s (%s)";

    /**
     * @return int
     */
    public function getType()
    {
        return Types::MISS;
    }

    /**
     * Parses an event line extracting miss specific info
     * First 3 characters are the team, followed by #NUM and player's last name
     * Next: type of shot, type of miss, location, distance
     *
     * @return bool
     */
    public function parse()
    {
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->type = $data['type'];
        $this->location = $data['location'];
        $this->distance = $data['distance'];
        $this->target = $data['target'];
        $this->team = new Team($data['team']);
        $this->player = new Player($data['number'], $data['player'], $this->team);

        $this->parsed = true;
        return true;
    }

    public function toArray()
    {
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'team' => $matches[1][0],
                'number' => $matches[2][0],
                'player' => $matches[3][0],
                'type' => $matches[4][0],
                'target' => $matches[5][0],
                'location' => $matches[6][0],
                'distance' => $matches[7][0]
            ];
        }

        return [];
    }

    /**
     * @return string
     */
    public function describe()
    {
        if ($this->parsed) {
            return sprintf(
                self::DESCRIBE,
                $this->period,
                $this->time,
                $this->type,
                $this->player->number,
                $this->player->name,
                $this->team->name,
                $this->distance,
                $this->location
            );
        }
    }
}