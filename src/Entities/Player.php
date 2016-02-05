<?php

namespace NHL\Entities;

/**
 * Class Player
 *
 * @package NHL\Entities
 */
class Player
{
    /** @var string $number */
    public $number;

    /** @var string $name */
    public $name;

    /** @var Team $team */
    public $team;

    /**
     * Reusable regex to parse a player in the TEAM #NO NAME format
     */
    const RX_WITH_TEAM = Team::RX." #(\\d+) ([A-Z\\h\\-\\']+)";
    const RX_NO_TEAM = "#(\\d+) ([A-Z\\h\\-\\']+)";

    /**
     * Player constructor.
     *
     * @param string $number
     * @param string $name
     * @param Team   $team
     */
    public function __construct($number, $name, $team)
    {
        $this->number = $number;
        $this->name = $name;
        $this->team = $team;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf("#%s %s (%s)", $this->number, $this->name, $this->team->name);
    }
}