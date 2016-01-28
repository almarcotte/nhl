<?php

namespace NHL\Entities;


class Team
{
    /** @var string $name */
    public $name;

    /**
     * Team constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->name;
    }

}