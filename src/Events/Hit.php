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
    const REGEX = "/([[:upper:]]+) #(\\d+) ([[:upper:]]+) HIT ([[:upper:]]+) #(\\d+) ([[:upper:]]+), ([A-Za-z\\.\\s]+)/i";
    const DESCRIBE = "[P%s: %s] #%s %s (%s) hit #%s %s (%s) in %s";

    /** @var Team $team1 The hitter's team */
    public $team1;

    /** @var Team $team2 The target's team */
    public $team2;

    /** @var Player $player1 The hitter */
    public $player1;

    /** @var Player $player2 The target */
    public $player2;

    /**
     * @return int
     */
    public function getType()
    {
        return Types::HIT;
    }

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
                $this->period,
                $this->time,
                $this->player1->number,
                $this->player1->name,
                $this->player1->team->name,
                $this->player2->number,
                $this->player2->name,
                $this->player2->team->name,
                $this->location
            );
        }
    }

}