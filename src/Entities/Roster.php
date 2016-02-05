<?php

namespace NHL\Entities;

/**
 * Class Roster
 *
 * Represents a group of player associated with a team
 *
 * @package NHL\Entities
 */
class Roster
{
    /** @var Player[] $players */
    protected $players;

    /** @var Team $team */
    protected $team;

    /**
     * @param Player $player
     */
    public function addPlayer(Player $player)
    {
        $this->players[] = $player;
    }

    /**
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param Team $team
     */
    public function setTeam(Team $team)
    {
        $this->team = $team;
    }

}