<?php

namespace NHL\Entities;


use NHL\Event;

class Game
{
    /** @var Team $home */
    public $home;

    /** @var Team $away */
    public $away;

    /** @var \DateTime $datetime */
    public $datetime;

    /** @var int $homeScore */
    public $homeScore;

    /** @var int $awayScore */
    public $awayScore;

    /** @var bool $wentOverTime */
    public $wentOverTime;

    /** @var Event[] $events */
    private $events;

    /** @var string $id Unique identifier for this game based on season and game number */
    private $id;

    /**
     * Game constructor.
     *
     * @param string $gameID
     */
    public function __construct($gameID)
    {
        $this->id = $gameID;
    }

    /**
     * Set the home team
     *
     * @param Team $team
     */
    public function setHomeTeam(Team $team)
    {
        $this->home = $team;
    }

    /**
     * Set the away team
     *
     * @param Team $team
     */
    public function setAwayTeam(Team $team)
    {
        $this->away = $team;
    }

    /**
     * @param int $home
     * @param int $away
     */
    public function setFinalScore($home, $away)
    {
        $this->homeScore = $home;
        $this->awayScore = $away;
    }

    /**
     * Add an event to this game
     *
     * @param Event $event
     */
    public function addEvent(Event $event)
    {
        $this->events[] = $event;
    }

    /**
     * @return \NHL\Event[]
     */
    public function getEvents()
    {
        return $this->events;
    }
}