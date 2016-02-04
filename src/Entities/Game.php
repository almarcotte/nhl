<?php

namespace NHL\Entities;

use NHL\Event;

/**
 * Class Game
 *
 * @package NHL\Entities
 */
class Game
{
    /** @var Team $home */
    public $home;

    /** @var Team $away */
    public $away;

    /** @var int $homeScore */
    public $homeScore;

    /** @var int $awayScore */
    public $awayScore;

    /** @var bool $wentOverTime */
    public $wentOverTime;

    /** @var string $date */
    public $date;

    /** @var int $attendance */
    public $attendance;

    /** @var string $venue */
    public $venue;

    /** @var string $startTime */
    public $startTime;

    /** @var string $startTimeZone */
    public $startTimeZone;

    /** @var string $endTime */
    public $endTime;

    /** @var string $endTimeZone */
    public $endTimeZone;

    /** @var string $season */
    public $season;

    /** @var Event[] $events */
    private $events;

    /** @var string $id Unique identifier for this game based on season and game number */
    public $id;

    /** @var string $shortID */
    public $shortID;

    const RX_DATE = "/([A-Za-z]+day [A-Za-z]+ \\d+ \\d+)/";
    const RX_ATTEND = "/Attendance (\\d+)(?:at)([A-Za-z\\h\\-]+)/";
    const RX_ENDSTART = "/(?:Start|End)(\\d+:\\d+)([A-Z]+)/";
    const RX_SCORETEAMS = "/(?:VISITOR|HOME)(\\d+)([A-Z]+)(?:Game)/";

    /**
     * Game constructor.
     *
     * @param string $gameID
     */
    public function __construct($gameID)
    {
        $this->id = $gameID;
        $this->shortID = mb_strcut($this->id, 11);
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

    /**
     * @param string $season
     */
    public function setSeason($season)
    {
        $this->season = $season;
    }
}