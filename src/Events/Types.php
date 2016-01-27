<?php

namespace NHL\Events;

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

    /**
     * @param string $event_string Represents the event type taken from the HTM file
     * @param string $line Entire line to parse
     * @return mixed
     */
    public static function makeTypeFromString($event_string, $line)
    {
        switch ($event_string) {
            case 'FAC':
                return self::FACEOFF;
            case 'HIT':
                return new Hit($line);
            case 'SHOT':
                return new Shot($line);
            case 'BLOCK':
                return self::BLOCK;
            case 'MISS':
                return new Miss($line);
            default:
                return self::NONE;
        }
    }
}