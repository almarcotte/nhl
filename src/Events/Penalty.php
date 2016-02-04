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
    const REGEX_PENALTY = "/".Player::RX_WITH_TEAM."@([A-Za-z\\h\\-\\(\\)]+)\\((\\d+) min\\)/";
    const REGEX_ZONE = "/, ([A-Za-z\\.]+ Zone)/";
    const REGEX_DRAWNBY = "/Drawn By: ".Player::RX_WITH_TEAM."/";
    const REGEX_SERVEDBY = "/ Served By: ".Player::RX_NO_TEAM."/";

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

    /** @var Player $servedByPlayer */
    public $servedByPlayer;

    /** @var bool $ledToPenaltyShot */
    public $ledToPenaltyShot;

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

        if (isset($data['drawn_team'])) {
            $this->drawnTeam = new Team($data['drawn_team']);
        }
        if (isset($data['drawn_number']) && isset($data['drawn_player'])) {
            $this->drawnPlayer = new Player($data['drawn_number'], $data['drawn_player'], $this->drawnTeam);
        }

        $this->player = new Player($data['penalty_number'], $data['penalty_player'], $this->team);

        if (isset($data['servedby_number']) && isset($data['servedby_player'])) {
            $this->servedByPlayer = new Player($data['servedby_number'], $data['servedby_player'], $this->team);
        }

        if (preg_match("/PS-/", $data['infraction'])) {
            $this->infraction = preg_replace("/PS-/", "", $data['infraction']);
            $this->ledToPenaltyShot = true;
        } else {
            $this->infraction = $data['infraction'];
            $this->ledToPenaltyShot = false;
        }

        $this->duration = $data['duration'];
        $this->location = isset($data['location']) ? $data['location'] : null;

        $this->parsed = true;

        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $penalty = [];
        $line = $this->line;

        // Check if it's served by anybody
        if (preg_match_all(self::REGEX_SERVEDBY, $line, $sbmatches)) {
            $penalty += [
                'servedby_number' => $sbmatches[1][0],
                'servedby_player' => $sbmatches[2][0]
            ];
            $line = preg_replace(self::REGEX_SERVEDBY, '', $line);
        }

        if (preg_match_all(self::REGEX_ZONE, $line, $zonematches)) {
            $penalty += [
                'location' => trim($zonematches[1][0])
            ];
        }

        if (preg_match_all(self::REGEX_PENALTY, $line, $pmatches)) {
            $penalty += [
                'penalty_team'   => $pmatches[1][0],
                'penalty_number' => $pmatches[2][0],
                'penalty_player' => $pmatches[3][0],
                'infraction'     => trim($pmatches[4][0]),
                'duration'       => trim($pmatches[5][0])
            ];
        }

        // Check if drawn by anybody
        if (preg_match_all(self::REGEX_DRAWNBY, $line, $dbmatches)) {
            $penalty += [
                'drawn_team'   => $dbmatches[1][0],
                'drawn_number' => $dbmatches[2][0],
                'drawn_player' => $dbmatches[3][0],
            ];
        }

        return $penalty;
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