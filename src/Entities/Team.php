<?php

namespace NHL\Entities;

/**
 * Class Team
 *
 * @package NHL\Entities
 */
class Team
{
    /** @var string $name */
    public $name;

    /** RegEx pattern for teams, used in a couple of events */
    const RX = "([A-Z\\.]{3})";

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