<?php

namespace NHL\Events;

use NHL\Entities\Player;
use NHL\Entities\Team;
use NHL\Event;

/**
 * Class Block
 * @package NHL\Events
 */
class Block extends Event
{

    const REGEX = "/([A-Z]{3}) #(\\d+) ([A-Z\\-\\h]+)(?: BLOCKED BY )([A-Z]{3}) #(\\d+) ([A-Z\\-\\h]+), (\\w+), ([\\w+.\\h]+)/";

    const DESCRIBE = "[P%s: %s] #%s %s (%s) blocked a %s shot from #%s %s (%s) in %s";

    /** @var Team $teamBlocked */
    public $teamBlocked;

    /** @var Team $teamBlocking */
    public $teamBlocking;

    /** @var Player $playerBlocked */
    public $playerBlocked;

    /** @var Player $playerBlocking */
    public $playerBlocking;

    /**
     * @inheritdoc
     * @return mixed
     */
    public function parse()
    {
        $data = $this->toArray();
        if (empty($data)) {
            $this->parsed = false;
            return false;
        }

        $this->location = $data['location'];
        $this->shotType = $data['shotType'];

        $this->teamBlocked = new Team($data['teamBlocked']);
        $this->teamBlocking = new Team($data['teamBlocking']);

        $this->playerBlocked = new Player($data['numberBlocked'], $data['playerBlocked'], $this->teamBlocked);
        $this->playerBlocking = new Player($data['numberBlocking'], $data['playerBlocking'], $this->teamBlocking);

        $this->parsed = true;
        return true;
    }

    /**
     * @inheritdoc
     * @return array
     */
    public function toArray()
    {
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'teamBlocked' => $matches[1][0],
                'numberBlocked' => $matches[2][0],
                'playerBlocked' => $matches[3][0],
                'teamBlocking' => $matches[4][0],
                'numberBlocking' => $matches[5][0],
                'playerBlocking' => $matches[6][0],
                'shotType' => $matches[7][0],
                'location' => $matches[8][0],
            ];
        }

        return [];
    }

    /**
     * @inheritdoc
     * @return string
     */
    public function describe()
    {
        // "[P%s: %s] #%s %s (%s) blocked a %s shot from #%s %s (%s) in %s";

        return sprintf(self::DESCRIBE,
            $this->period,
            $this->time,
            $this->playerBlocking->number,
            $this->playerBlocking->name,
            $this->playerBlocking->team->name,
            $this->shotType,
            $this->playerBlocked->number,
            $this->playerBlocked->name,
            $this->playerBlocked->team->name,
            $this->location
        );

    }


}