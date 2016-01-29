<?php

namespace NHL;

/**
 * Class Event
 *
 * @package NHL
 */
abstract class Event
{
    /** @var int $eventNumber Event number (first face off is 1) */
    public $eventNumber;

    /** @var int $eventPeriod Period number */
    public $eventPeriod;

    /** @var string $eventTime Event timestamp */
    public $eventTime;

    /** @var string $eventType */
    public $eventType;

    /** @var bool $parsed */
    public $parsed;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->eventType;
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
    public abstract function parse();

    /**
     * Parses the event line and returns an array
     *
     * @return array
     */
    public abstract function toArray();

    /**
     * @param $number
     */
    public function setEventNumber($number)
    {
        $this->eventNumber = $number;
    }

    /**
     * @param $period
     */
    public function setPeriod($period)
    {
        $this->eventPeriod = $period;
    }

    /**
     * @param $time
     */
    public function setTime($time)
    {
        $this->eventTime = $time;
    }

    /**
     * Returns the event in a human readable format
     *
     * @return string
     */
    public abstract function describe();
}