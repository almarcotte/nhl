<?php

namespace NHL\Contracts;

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

}