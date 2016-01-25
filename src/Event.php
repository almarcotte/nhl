<?php

namespace NHL;

use NHL\Events\Types;

class Event
{
    protected $number;
    protected $period;
    protected $time;

    /**
     * @return int
     */
    public function getType()
    {
        return Types::NONE;
    }

    /**
     * @param $line
     * @return mixed
     */
    public function parseLine($line)
    {
        return $line;
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