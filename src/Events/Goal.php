<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Goal
 *
 * @package NHL\Events
 */
class Goal extends Event
{
    const GOAL_REGEX = "/([A-Z]{3})(?:\\h{1}#)(\\d+)(?:\\h{1})([A-Z\\h-]+)\\(\\d\\),\\h(\\w+),\\h([\\w\\.\\h]+), (\\d+) ft./";
    const ASSIST_REGEX = "/(?:[:|;]) #(\\d+)\\h([A-Z\\h\\-]+)/";
    const DESCRIBE = "[P%s: %s] Goal (%s) by %s from %s ft. in %s. Assists: %s";

    /**
     * @var Player[] $assists
     */
    public $assists = [];

    /** @var string $eventType */
    public $eventType = Types::GOAL;

    /** @var Team $team */
    public $team;

    /** @var Player $player */
    public $player;

    /** @var string $location */
    public $location;

    /** @var string $distance */
    public $distance;

    /** @var string $shotType */
    public $shotType;

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

        $this->team = new Team($data['goal']['team']);
        $this->player = new Player($data['goal']['number'], $data['goal']['name'], $this->team);
        $this->location = $data['goal']['location'];
        $this->distance = $data['goal']['distance'];
        $this->shotType = $data['goal']['type'];
        foreach($data['assists'] as $assist) {
            $this->assists[] = new Player($assist['number'], $assist['name'], $this->team);
        }

        $this->parsed = true;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $assists = [];
        $goal = [];

        if (preg_match_all(self::ASSIST_REGEX, $this->line, $assist_matches)) {
            $numbers = array_values($assist_matches[1]);
            $names = array_values($assist_matches[2]);
            for ($i=0; $i < count($numbers); $i++) {
                $assists[] = [
                    'number' => $numbers[$i],
                    'name' => $names[$i]
                ];
            }
        }

        if (preg_match_all(self::GOAL_REGEX, $this->line, $goal_matches)) {
            $goal = [
                'team' => $goal_matches[1][0],
                'number' => $goal_matches[2][0],
                'name' => $goal_matches[3][0],
                'type' => $goal_matches[4][0],
                'location' => $goal_matches[5][0],
                'distance' => $goal_matches[6][0],
            ];
        }

        return [
            'goal' => $goal,
            'assists' => $assists
        ];
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
                $this->shotType,
                $this->player,
                $this->distance,
                $this->location,
                implode(', ', $this->assists)
            );
        }
    }
}