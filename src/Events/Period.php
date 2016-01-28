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

    const REGEX = "/Period (End|Start)- Local time: (\\d+:\\d+) ([A-Z]+)/";
    const DESCRIBE = "[P%s: %s] %s of Period at %s %s local time";

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
        $this->eventType = ($data['event'] == 'Start') ? Types::PERIODSTART : Types::PERIODEND;

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
                'time' => $matches[2][0],
                'timezone' => $matches[3][0]
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
                $this->eventType == Types::PERIODSTART ? 'Start' : 'End',
                $this->time,
                $this->timezone
            );
        }
    }

}