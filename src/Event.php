<?php

namespace NHL;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Events\Types;

class Event
{
    public $number;

    public $period;

    public $time;

    public $line;

    /** @var Team $team */
    public $team;

    /** @var Player $player */
    public $player;

    /** @var string $shotType */
    public $shotType;

    /** @var string $missType */
    public $missType;

    /** @var string $location */
    public $location;

    /** @var string $distance */
    public $distance;

    /** @var string $type */
    public $type;

    /** @var string $target */
    public $target;

    /** @var bool $parsed */
    public $parsed;

    /**
     * @return int
     */
    public function getType()
    {
        return Types::NONE;
    }

    public function __construct($line)
    {
        $this->line = $line;
    }

    /**
     * @return mixed
     */
    public function parse()
    {
        return $this->line;
    }

    /**
     * @param $number
     */
    public function setEventNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @param $period
     */
    public function setPeriod($period)
    {
        $this->period = $period;
    }

    /**
     * @param $time
     */
    public function setTime($time)
    {
        $this->time = $time;
    }

    /**
     * @return string
     */
    public function describe()
    {
        return '';
    }

    /**
     * @param $value
     * @return array
     */
    protected function parsePlayerAndTeam($value)
    {
        $exploded = explode(' ', $value);
        return [
            'team' => $exploded[0],
            'number' => $exploded[1],
            'player' => $exploded[2]
        ];
    }
}