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
    const REGEX = "/([A-Z]{3}) #(\\d+) ([A-Z\\s\\-\\.]+) HIT ([A-Z]{3}) #(\\d+) ([A-Z\\s\\-\\.]+), ([A-Za-z\\.\\s]+)/";
    const DESCRIBE = "[P%s: %s] %s hit %s in %s";

    /** @var Team $team1 The hitter's team */
    public $team1;

    /** @var Team $team2 The target's team */
    public $team2;

    /** @var Player $player1 The hitter */
    public $player1;

    /** @var Player $player2 The target */
    public $player2;

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

        $this->team1 = new Team($data['team1']);
        $this->player1 = new Player($data['number1'], $data['player1'], $this->team1);
        $this->team2 = new Team($data['team2']);
        $this->player2 = new Player($data['number2'], $data['player2'], $this->team2);
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
                $this->player1,
                $this->player2,
                $this->location
            );
        }
    }

}