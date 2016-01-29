<?php

namespace NHL\Entities;


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
}