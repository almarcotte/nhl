<?php

namespace NHL;


class Config
{
    /** @var string $ */
    private $file;

    /** @var array $data */
    private $data;


    /**
     * Config constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;

        $this->data = parse_ini_file($this->file, true);
    }

    /**
     * @param $section
     * @param mixed $field
     * @return mixed
     */
    public function get($section, $field)
    {
        return isset($this->data[$section][$field]) ? $this->data[$section][$field] : null;
    }

    /**
     * @param mixed $section
     * @param mixed $field
     * @param mixed $value
     * @return $this
     */
    public function set($section, $field, $value)
    {
        $this->data[$section][$field] = $value;

        return $this;
    }

    /**
     * Returns an array of available fields for general use
     *
     * @return array
     */
    public function getAllFields()
    {
        return [
            // General settings, most of these can be change through the command line
            'general' => ['download-only', 'parse-only', 'season', 'verbose', 'quick', 'exporter', 'files'],
            // Exporter-specific settings
            'stdout' => ['show-summary'],
            'file' => ['output-dir']
        ];
    }

}