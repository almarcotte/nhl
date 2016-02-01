<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Penalty
 *
 * @package NHL\Events
 */
class Penalty extends Event
{
    const PLAYERS_REGEX = "/([A-Z]{3})(?:\\h{1}#)(\\d{1,2})(?:\\h{1})([A-Z \\-]+)/";
    const DETAILS_REGEX = "/(?:@)([A-Za-z\\h]+)(?:\\((\\d+) min\\))(?:[, ]+)(?:([A-Za-z .]+)Drawn By: )/";
    const DESCRIBE = "[P%s: %s] %s %s minutes for %s in %s drawn by %s";

    /** @var string $duration */
    public $duration;

    /** @var string $infraction */
    public $infraction;

    /** @var Player $drawnPlayer */
    public $drawnPlayer;

    /** @var Team $drawnTeam */
    public $drawnTeam;

    /** @var string $eventType */
    public $eventType = Types::PENALTY;

    /** @var Team $team */
    public $team;

    /** @var Player $player */
    public $player;

    /** @var string $location */
    public $location;

    /**
     * @inheritdoc
     */
    public function parse()
    {
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->team = new Team($data['penalty_team']);
        $this->drawnTeam = new Team($data['drawn_team']);

        $this->player = new Player($data['penalty_number'], $data['penalty_player'], $this->team);
        $this->drawnPlayer = new Player($data['drawn_number'], $data['drawn_player'], $this->drawnTeam);

        $this->infraction = $data['infraction'];
        $this->duration = $data['duration'];
        $this->location = $data['location'];

        $this->parsed = true;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        if (preg_match_all(self::PLAYERS_REGEX, $this->line, $players)
            && preg_match_all(self::DETAILS_REGEX, $this->line, $details)) {
            return [
                'penalty_team' => $players[1][0],
                'penalty_number' => $players[2][0],
                'penalty_player' => $players[3][0],
                'drawn_team' => $players[1][1],
                'drawn_number' => $players[2][1],
                'drawn_player' => $players[3][1],
                'infraction' => trim($details[1][0]),
                'duration' => trim($details[2][0]),
                'location' => trim($details[3][0])
            ];
        } else {
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function describe()
    {
        if ($this->parsed) {
            return sprintf(
                self::DESCRIBE,
                $this->eventPeriod,
                $this->eventTime,
                $this->player,
                $this->duration,
                $this->infraction,
                $this->location,
                $this->drawnPlayer
            );
        }
    }

}