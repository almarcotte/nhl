<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Miss
 *
 * @package NHL\Events
 */
class Miss extends Event
{
    const REGEX = "/".Player::RX_WITH_TEAM.", ([A-Za-z\\.\\-\\h]+), ([A-Za-z\\.\\-\\h]+), ([A-Za-z\\.\\-\\h]+), (\\d+) ft./";
    const DESCRIBE = "[P%s: %s] Missed %s shot %s by %s from %s (%s)";

    /** @var string $eventType */
    public $eventType = Types::MISS;

    /** @var string $shotType */
    public $shotType;

    /** @var string $location */
    public $location;

    /** @var int $distance */
    public $distance;

    /** @var string $target */
    public $target;

    /** @var Player $player */
    public $player;

    /** @var Team $team */
    public $team;

    /** @var bool $isPenaltyShot */
    public $isPenaltyShot;

    /**
     * @inheritdoc
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
        $this->target = $data['target'];
        $this->team = new Team($data['team']);
        $this->player = new Player($data['number'], $data['player'], $this->team);
        $this->isPenaltyShot = $data['isPenalty'];

        $this->parsed = true;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'team'      => $matches[1][0],
                'number'    => $matches[2][0],
                'player'    => $matches[3][0],
                'type'      => $matches[4][0],
                'target'    => $matches[5][0],
                'location'  => $matches[6][0],
                'distance'  => $matches[7][0],
                'isPenalty' => false
            ];
        } else if (preg_match_all(Shot::REGEX_PENALTYSHOT, $this->line, $matches)) {
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
        }

        return [];
    }

    /**
     * @inheritdoc
     */
    public function describe()
    {
        if ($this->parsed) {
            return sprintf(
                self::DESCRIBE,
                $this->eventPeriod,
                $this->eventTime,
                $this->shotType,
                $this->isPenaltyShot ? '(Penalty)' : '',
                $this->player,
                $this->distance,
                $this->location
            );
        }
    }
}