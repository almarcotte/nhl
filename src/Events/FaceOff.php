<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class FaceOff
 * @package NHL\Events
 */
class FaceOff extends Event
{

    const REGEX = "/([[:upper:]]+) won ([A-Za-z\\.\\s]+) - ([[:upper:]]+) #(\\d+) ([[:upper:]\\s]+) vs ([[:upper:]]+) #(\\d+) ([[:upper:]\\s]+)/i";
    const DESCRIBE = "[P%s: %s] %s won faceoff in %s - %s vs %s";

    /** @var Team $team_won */
    public $team_won;

    /** @var Team $home_team */
    public $home_team;

    /** @var Player $home_player */
    public $home_player;

    /** @var Team $away_team */
    public $away_team;

    /** @var Player $away_player */
    public $away_player;

    /** @var string $eventType */
    public $eventType = Types::FACEOFF;
    public $location;

    /**
     * @inheritdoc
     * @return bool
     */
    public function parse()
    {
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->team_won = new Team($data['team_won']);
        $this->location = $data['location'];
        $this->home_team = new Team($data['home_team']);
        $this->away_team = new Team($data['away_team']);
        $this->home_player = new Player($data['home_number'], $data['home_player'], $this->home_team);
        $this->away_player = new Player($data['away_number'], $data['away_player'], $this->away_team);

        $this->parsed = true;
        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        //MTL won Off. Zone - MTL #51 DESHARNAIS vs TOR #16 SPALING
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'team_won' => $matches[1][0],
                'location' => $matches[2][0],
                'home_team' => $matches[3][0],
                'home_number' => $matches[4][0],
                'home_player' => $matches[5][0],
                'away_team' => $matches[6][0],
                'away_number' => $matches[7][0],
                'away_player' => $matches[8][0],
            ];
        }
    }

    public function describe()
    {
        if ($this->parsed) {
            return sprintf(self::DESCRIBE,
                $this->eventPeriod,
                $this->eventTime,
                $this->team_won,
                $this->location,
                $this->home_player,
                $this->away_player
            );
        }
    }

}