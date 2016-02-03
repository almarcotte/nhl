<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Hit
 * @package NHL\Events
 */
class Hit extends Event
{
    const REGEX = "/([A-Z\\.]{3}) #(\\d+) ([A-Z\\s\\-\\.]+) HIT ([A-Z\\.]{3}) #(\\d+) ([A-Z\\s\\-\\.]+), ([A-Za-z\\.\\s]+)/";
    const DESCRIBE = "[P%s: %s] %s hit %s in %s";

    /** @var Team $teamHitting The hitter's team */
    public $teamHitting;

    /** @var Team $teamHit The target's team */
    public $teamHit;

    /** @var Player $playerHitting The hitter */
    public $playerHitting;

    /** @var Player $playerHit The target */
    public $playerHit;

    /** @var string $location */
    public $location;

    /** @var string $eventType */
    public $eventType = Types::HIT;

    /**
     * @inheritdoc
     * @return bool
     */
    public function parse()
    {
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->teamHitting = new Team($data['team1']);
        $this->playerHitting = new Player($data['number1'], $data['player1'], $this->teamHitting);
        $this->teamHit = new Team($data['team2']);
        $this->playerHit = new Player($data['number2'], $data['player2'], $this->teamHit);
        $this->location = $data['location'];

        $this->parsed = true;

        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        //MTL #79 MARKOV HIT TOR #15 PARENTEAU, Def. Zone
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'team1' => $matches[1][0],
                'number1' => $matches[2][0],
                'player1' => $matches[3][0],
                'team2' => $matches[4][0],
                'number2' => $matches[5][0],
                'player2' => $matches[6][0],
                'location' => $matches[7][0]
            ];
        }
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function describe()
    {
        if ($this->parsed) {
            return sprintf(self::DESCRIBE,
                $this->eventPeriod,
                $this->eventTime,
                $this->playerHitting,
                $this->playerHit,
                $this->location
            );
        }
    }

}