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
    const REGEX = "/".Team::RX." ([A-Z]+) - ".Player::RX_NO_TEAM.", ([A-Za-z\\h\\-]+), ([A-Za-z\\. ]+), (\\d+) ft./";

    const REGEX_PENALTYSHOT = "/".Player::RX_WITH_TEAM
                              .", (Penalty) Shot, ([A-Za-z\\h\\-\\.]+), ([A-Za-z\\h\\-\\.]+), ([A-Za-z\\h\\-\\.]+), (\\d+) ft./";

    const REGEX_PENALTYONGOAL = "/".Team::RX." ([A-Z]+) - ".Player::RX_NO_TEAM
                                .", Penalty Shot, ([A-Za-z\\h\\-]+), ([A-Za-z\\h\\-\\.]+), (\\d+) ft./";

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

    /** @var bool $isPenaltyShot */
    public $isPenaltyShot;

    /**
     * Parse a SHOT event line.
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

        $this->shotType = $data['type'];
        $this->location = $data['location'];
        $this->distance = $data['distance'];
        $this->team = new Team($data['team']);
        $this->target = $data['target'];
        $this->player = new Player($data['number'], $data['player'], $this->team);

        $this->isPenaltyShot = $data['isPenalty'];

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
                'team'      => $matches[1][0],
                'target'    => $matches[2][0],
                'number'    => $matches[3][0],
                'player'    => $matches[4][0],
                'type'      => $matches[5][0],
                'location'  => $matches[6][0],
                'distance'  => $matches[7][0],
                'isPenalty' => false
            ];
        } else if (preg_match_all(self::REGEX_PENALTYONGOAL, $this->line, $matches)) {
            return [
                'team'      => $matches[1][0],
                'target'    => $matches[2][0],
                'number'    => $matches[3][0],
                'player'    => $matches[4][0],
                'type'      => $matches[5][0],
                'location'  => $matches[6][0],
                'distance'  => $matches[7][0],
                'isPenalty' => true
            ];
        } else if (preg_match_all(self::REGEX_PENALTYSHOT, $this->line, $matches)) {
            return [
                'team'      => $matches[1][0],
                'number'    => $matches[2][0],
                'player'    => $matches[3][0],
                'type'      => $matches[5][0],
                'target'    => $matches[6][0],
                'location'  => $matches[7][0],
                'distance'  => $matches[8][0],
                'isPenalty' => true
            ];
        } else {
            return [];
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