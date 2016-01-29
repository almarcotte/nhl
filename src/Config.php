<?php

namespace NHL;


class Config
{
    /** @var string $ */
    private $file;

    /** @var array $data */
    private $data;

    /** @var array $section */
    private $section;

    /**
     * Config constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->file = $file;
        $this->parseConfigFile();
    }

    /**
     * Parses the ini file and saves the data
     */
    private function parseConfigFile()
    {
        $this->data = parse_ini_file($this->file, true);
    }

    /**
     * @param $field
     *
     * @return $this
     */
    public function __get($field)
    {
        if (!is_array($this->data[$field])) {
            $this->section = null;
            return $this->data[$field];
        } else {
            if (is_null($this->section)) {
                $this->section = $this->data[$field];
                return $this;
            }
            $this->section = $this->section[$this->data[$field]];
            return $this;
        }
    }

}