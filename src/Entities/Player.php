<?php

namespace NHL\Entities;


class Player
{
    /** @var string $number */
    public $number;

    /** @var string $name */
    public $name;

    /** @var Team $team */
    public $team;

    /**
     * Player constructor.
     * @param string $number
     * @param string $name
     * @param Team $team
     */
    public function __construct($number, $name, $team)
    {
        $this->number = $number;
        $this->name = ucfirst($name);
        $this->team = $team;
    }
}