<?php

namespace NHL;

use DOMDocument;
use DOMXPath;
use League\CLImate\CLImate;
use NHL\Events\Shot;
use NHL\Events\Types;
use NHL\Exceptions\NHLParserException;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\AbstractNode;
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
     * Makes sure we have files to parse. If not only parsing, initiate the download
     *
     * @throws NHLParserException
     */
    private function prepareFiles()
    {
        // Not downloading, make sure files exist to parse
        if (is_null($this->downloader)) {
            // Try and parse existing files, no additional downloads
            if (!$this->climate->arguments->defined('files')) {
                throw new NHLParserException("Couldn't find setting for downloaded file location.\n");
            }
            if (!is_dir($this->climate->arguments->get('files'))) {
                throw new NHLParserException("The path provided for files isn't a directory.\n");
            }
        } else {
            $this->downloader->download();
        }
    }

    /**
     * Parses files
     *
     * @return bool
     * @throws NHLParserException
     */
    public function parse()
    {
        ini_set('memory_limit', -1); // uh oh

        $this->prepareFiles();
        $files = $this->getAllFileNames();

        foreach($files as $filename) {
            $this->processFile($filename);
        }

        return true;
    }

    /**
     * Gets all file names in the data file directory
     *
     * @return array
     */
    private function getAllFileNames()
    {

        $directory = new RecursiveDirectoryIterator(
            $this->climate->arguments->get('files')
        );
        $iterator = new RecursiveIteratorIterator($directory);
        $regex = new RegexIterator($iterator, '/^.+\.HTM$/i', RecursiveRegexIterator::GET_MATCH);

        $files = array_keys(iterator_to_array($regex));

        usort($files, function ($a, $b) {
            return strcmp($a, $b);
        });

        return $files;
    }

    /**
     * @param $filename
     */
    private function processFile($filename)
    {
        $this->climate->out("Processing " . $filename);

        $dom = new Dom();
        $dom->loadFromFile($filename);
        $lines = [];
        $events = $dom->find('tr.evenColor');
        $progress = $this->climate->progress(count($events));
        /** @var AbstractNode $tr */
        foreach($events as $tr) {
            $lineContent = [];
            $lineCount = 0;
            $progress->advance();
            /** @var AbstractNode $td */
            foreach($tr->getChildren() as $td) {
                $value = trim(str_replace('&nbsp;', '@', $td->text)); // clean up the line, adding @ to make parsing a bit easier for certain events
                if ($value) {
                    $lineCount++;
                    // Each event is actually 6 lines
                    $lineContent[] = $value;
                    if ($lineCount % 6 == 0) {
                        $lines[] = $lineContent;
                        $lineContent = [];
                    }
                }
            }
        }

        foreach($lines as $line) {
            $this->createParsedEvent($line);
        }
    }

    /**
     * @param $line
     * @return bool
     */
    private function createParsedEvent($line)
    {
        if (count($line) != 6) {
            return false;
        }

        if (in_array($line[4], Types::getSupported())) {
            /** @var Event $event */
            $event = Types::makeTypeFromString($line[4], $line[5]);

            $event->setEventNumber($line[0]);
            $event->setPeriod($line[1]);
            $event->setTime($line[3]);

            $event->parse();
            $this->climate->out($event->describe());
        } else {
            $this->climate->out("Unsupported event: " . $line[4]);
        }
    }

}