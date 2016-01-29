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
    public $teamWon;

    /** @var Team $firstTeam */
    public $firstTeam;

    /** @var Player $firstPlayer */
    public $firstPlayer;

    /** @var Team $secondTeam */
    public $secondTeam;

    /** @var Player $secondPlayer */
    public $secondPlayer;

    /** @var string $eventType */
    public $eventType = Types::FACEOFF;

    /** @var string $location */
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

        $this->teamWon = new Team($data['team_won']);
        $this->location = $data['location'];
        $this->firstTeam = new Team($data['home_team']);
        $this->secondTeam = new Team($data['away_team']);
        $this->firstPlayer = new Player($data['home_number'], $data['home_player'], $this->firstTeam);
        $this->secondPlayer = new Player($data['away_number'], $data['away_player'], $this->secondTeam);

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
                $this->teamWon,
                $this->location,
                $this->firstPlayer,
                $this->secondPlayer
            );
        }
    }

}