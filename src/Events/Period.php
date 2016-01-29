<?php

namespace NHL\Events;


use NHL\Event;

/**
 * Class Period
 *
 * Represents both the start and end of a period
 *
 * @package NHL\Events
 */
class Period extends Event
{

    const REGEX = "/(Period|Game) (End|Start)- Local time: (\\d+:\\d+) ([A-Z]+)/";
    const DESCRIBE = "[P%s: %s] %s of %s at %s %s local time";

    /** @var string $eventType */
    public $eventType = Types::PERIODSTART;

    public $time;
    public $timezone;

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

        $this->time = $data['time'];
        $this->timezone = $data['timezone'];
        if ($data['event'] == 'Game') {
            $this->eventType = Types::GAMEEND;
        } else {
            $this->eventType = ($data['result'] == 'Start') ? Types::PERIODSTART : Types::PERIODEND;
        }

        $this->parsed = true;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        if (preg_match_all(self::REGEX, $this->line, $matches)) {
            return [
                'event' => $matches[1][0],
                'result' => $matches[2][0],
                'time' => $matches[3][0],
                'timezone' => $matches[4][0]
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
            $what = in_array($this->eventType, [Types::PERIODSTART, Types::PERIODEND]) ? 'Period' : 'Game';
            $endsOrStarts = $this->eventType !== Types::GAMEEND ? ($this->eventType == Types::PERIODSTART ? 'Start' : 'End') : 'End';
            //const DESCRIBE = "[P%s: %s] %s of %s at %s %s local time";
            return sprintf(
                self::DESCRIBE,
                $this->eventPeriod,
                $this->eventTime,
                $endsOrStarts,
                $what,
                $this->time,
                $this->timezone
            );
        }
    }

}