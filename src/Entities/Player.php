<?php

namespace NHL\Entities;


class Player
{
    protected $number;
    protected $name;
    protected $team;

    /**
     * @param $number
     * @param $name
     * @return static
     */
    public static function create($number, $name, Team $team)
    {
        $number = str_replace('#', '', $number);
        return new static($number, $name, $team);
    }

    public function __construct($number, $name, $team)
    {
        $this->number = $number;
        $this->name = $name;
        $this->team = $team;
    }

    /**
     * @return mixed
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * @param mixed $number
     */
    public function setNumber($number)
    {
        $this->number = $number;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @param mixed $team
     */
    public function setTeam($team)
    {
        $this->team = $team;
    }

}