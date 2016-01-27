<?php

namespace NHL\Events;

/**
 * Class Types
 *
 * @package NHL\Events
 */
class Types
{
    const NONE = -1;
    const PERIODSTART = 'PSTR';
    const FACEOFF = 'FAC';
    const HIT = 'HIT';
    const SHOT = 'SHOT';
    const BLOCK = 'BLOCK';
    const MISS = 'MISS';
    const STOP = 'STOP';
    const GIVE = 'BLOCK';
    const TAKE = 'TAKE';
    const GOAL = 'GOAL';
    const GAMEEND = 'GEND';
    const PERIODEND = 'PEND';
    const PENALTY = 'PENL';

    /**
     * Returns all the supported event types
     *
     * @return array
     */
    public static function getSupported() {
        return [
            Types::SHOT,
            Types::MISS,
            Types::HIT,
            Types::FACEOFF,
            Types::PENALTY
        ];
    }

    /**
     * @param string $event_string Represents the event type taken from the HTM file
     * @param string $line         Entire line to parse
     *
     * @return mixed
     */
    public static function makeTypeFromString($event_string, $line)
    {
        switch ($event_string) {
            case self::FACEOFF:
                return new FaceOff($line);
            case self::HIT:
                return new Hit($line);
            case self::SHOT:
                return new Shot($line);
            case self::BLOCK:
                return self::BLOCK;
            case self::MISS:
                return new Miss($line);
            case self::PENALTY:
                return new Penalty($line);
            default:
                return self::NONE;
        }
    }
}