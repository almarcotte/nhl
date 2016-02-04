<?php

namespace NHL\Contracts;

use NHL\Command;
use NHL\Exceptions\ParserException;
use PHPHtmlParser\Dom;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

/**
 * Class Parser
 * Parses NHL.com game data files to a readable format
 *
 * @package NHL
 */
abstract class Parser
{
    /** @var Command $command */
    protected $command;

    /** @var string $filePattern */
    protected $filePattern = null;

    /** @var string $name */
    public $name = "Abstract";

    /**
     * Parser constructor.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        $this->command = $command;
    }

    /**
     * Makes sure we have files to parse.
     *
     * @throws ParserException
     */
    protected function prepareFiles()
    {
        // Try and parse existing files, no additional downloads
        if (!$this->command->config->get('general', 'files')) {
            throw new ParserException("Couldn't find setting for downloaded file location.\n");
        }
        if (!is_dir($this->command->config->get('general', 'files'))) {
            throw new ParserException("The path provided for files isn't a directory.\n");
        }
    }

    public abstract function parse();

    /**
     * Parses the given file and returns a Game object
     *
     * @param string $filename
     */
    abstract protected function processFile($filename);

    /**
     * Gets all file names in the data file directory
     *
     * @return array
     */
    protected function getAllFileNames()
    {
        $directory = new RecursiveDirectoryIterator($this->command->config->get('general', 'files'));
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, $this->filePattern, RecursiveRegexIterator::GET_MATCH);

        $files = array_keys(iterator_to_array($regex));

        usort($files, function ($a, $b) {
            return strcmp($a, $b);
        });

        return $files;
    }

}