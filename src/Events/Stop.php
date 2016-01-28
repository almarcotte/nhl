<?php

namespace NHL\Events;

use NHL\Event;

/**
 * Class Stop
 *
 * @package NHL\Events
 */
class Stop extends Event
{

    const DESCRIBE = "[P%s: %s] Stop: %s";
    const ADDITIONAL_DESCRIBE = " (Also: %s)";

    /** @var string $reason */
    public $reason;

    /** @var string|null $other */
    public $other;

    /** @var string $eventType */
    public $eventType = Types::STOP;

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

        $this->reason = $data['reason'];
        $this->other = $data['other'];

        $this->parsed = true;
        return true;
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        $reasons = explode(',', $this->line);
        return [
            'reason' => $reasons[0],
            'other' => count($reasons) == 2 ? $reasons[1] : null
        ];
    }

    /**
     * @inheritdoc
     */
    public function describe()
    {
        $output = sprintf(
            self::DESCRIBE,
            $this->eventPeriod,
            $this->eventTime,
            $this->reason
        );

        if (!is_null($this->other)) {
            $output .= sprintf(self::ADDITIONAL_DESCRIBE, $this->other);
        }

        return $output;
    }

}