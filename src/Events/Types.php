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
     * @param $string
     * @return mixed
     */
    public static function makeTypeFromString($string)
    {
        switch($string) {
            case 'FAC':
                return self::FACEOFF;
            case 'HIT':
                return self::HIT;
            case 'SHOT':
                return new Shot();
            case 'BLOCK':
                return self::BLOCK;
            case 'MISS':
                return new Miss();
            default:
                return self::NONE;
        }
    }
}