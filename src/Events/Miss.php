<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

class Miss extends Event
{
    protected $shotTypes = [
        'Wrist'
    ];

    /** @var Team $team */
    protected $team;

    /** @var  Player $player */
    protected $player;

    /** @var string $shotType */
    protected $shotType;

    /** @var string $missType */
    protected $missType;

    /** @var string $location */
    protected $location;

    /** @var string $distance */
    protected $distance;

    /**
     * @return int
     */
    public function getType()
    {
        return Types::MISS;
    }

    /**
     * Parses an event line extracting miss specific info
     * First 3 characters are the team, followed by #NUM and player's last name
     * Next: type of shot, type of miss, location, distance
     *
     * @param $line
     * @return mixed|void
     */
    public function parseLine($line)
    {
        // MTL #74 EMELIN, Wrist, Wide of Net, Off. Zone, 62 ft.
        $exploded = explode(',', $line);
        $teamAndPlayer = $this->parsePlayerAndTeam($exploded[0]);

        $this->team = new Team($teamAndPlayer['team']);
        $this->player = Player::create($teamAndPlayer['number'], $teamAndPlayer['player'], $this->team);

        $this->shotType = $exploded[1];
        $this->missType = $exploded[2];
        $this->location = $exploded[3];
        $this->distance = $exploded[4];
    }

    /**
     * @return string
     */
    public function describe()
    {
        return $this->player->getName() . "#" . $this->player->getNumber() . " from "
            . $this->team->getName() . " missed a " . $this->shotType . " shot "
            . $this->missType . " from " . $this->distance . " in " . $this->location;
    }
}