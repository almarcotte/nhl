<?php

namespace NHL;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Events\Types;

class Event
{
    /** @var int $number Event number (first face off is 1) */
    public $number;

    /** @var int $period Period number */
    public $period;

    /** @var string $time Event timestamp */
    public $time;

    /** @var string $line Unparsed line */
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
     * Parses the event line for this event
     *
     * @return bool
     */
    public function parse()
    {
        return true;
    }

    /**
     * Parses the event line and returns an array
     *
     * @return array
     */
    public function toArray()
    {
        return [];
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
     * Returns the event in a human readable format
     *
     * @inheritdoc
     * @return string
     */
    public function describe()
    {
        return '';
    }
}