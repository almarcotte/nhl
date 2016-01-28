<?php

namespace NHL\Events;


use NHL\Event;

class Period extends Event
{

    /**
     * @inheritdoc
     */
    public function parse()
    {
        return parent::parse();
    }

    /**
     * @inheritdoc
     */
    public function toArray()
    {
        return parent::toArray();
    }

    /**
     * @inheritdoc
     */
    public function describe()
    {
        return $this->line;
    }

}