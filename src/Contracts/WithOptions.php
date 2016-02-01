<?php

namespace NHL\Contracts;

use NHL\Config;

/**
 * Class WithOptions
 *
 * Allows an exporter to have option configurable through the command line
 *
 * @package NHL\Contracts
 */
trait WithOptions
{
    /** @var array $options */
    protected $options;

    /**
     * Set the options for the exporter based on Config
     *
     * @param array $options
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
    }

    /**
     * @param $option
     *
     * @return bool
     */
    public function hasOption($option)
    {
        return isset($this->options[$option]);
    }

    /**
     * @param string $option
     * @param mixed  $value
     */
    public function setOption($option, $value)
    {
        $this->options[$option] = $value;
    }

    /**
     * @param string $option
     *
     * @return mixed
     */
    public function getOption($option) {
        return isset($this->options[$option]) ? $this->options[$option] : null;
    }

}