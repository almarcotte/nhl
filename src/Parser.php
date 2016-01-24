<?php

namespace NHL;

use League\CLImate\CLImate;
use NHL\Exceptions\NHLParserException;

/**
 * Class Parser
 * Parses NHL.com game data files to a readable format
 *
 * @package NHL
 */
class Parser
{
    /** @var Command $command */
    protected $command;

    /** @var CLImate $climate */
    protected $climate;

    /** @var array $options */
    protected $options = [];

    /**
     * Parser constructor.
     * @param Command $command
     * @param CLImate $climate
     * @param Downloader $downloader
     */
    public function __construct(Command $command, CLImate $climate, Downloader $downloader = null)
    {
        $this->command = $command;
        $this->climate = $climate;
        $this->downloader = $downloader;
    }

    /**
     * Sets the parser-specific options
     *
     * @param array $options
     */
    public function setOptions(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Parses files
     *
     * @return bool
     * @throws NHLParserException
     */
    public function parse()
    {
        if (is_null($this->downloader)) {
            // Try and parse existing files, no additional downloads
            if (!$this->climate->arguments->defined('files')) {
                throw new NHLParserException("Couldn't find setting for downloaded file location.\n");
            }
            if (!is_dir($this->climate->arguments->get('files'))) {
                throw new NHLParserException("The path provided for files isn't a directory.\n");
            }
        }
        return true;
    }

}