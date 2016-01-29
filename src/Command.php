<?php

namespace NHL;

use League\CLImate\CLImate;
use NHL\Exceptions\NHLDownloaderException;
use NHL\Exceptions\NHLParserException;
use NHL\Exporters\PlainText;

/**
 * Class Command
 * Represents the command line interface, main entry point of the entire app
 *
 * @package NHL
 */
class Command
{
    const DESCRIPTION = "An NHL.com data file processor";

    /** @var CLImate $climate */
    protected $climate;

    /** @var Downloader $downloader */
    protected $downloader;

    /**
     * Command constructor.
     * @param CLImate $climate
     */
    public function __construct(CLImate $climate)
    {
        $this->climate = $climate;
        $this->createCommandLineArguments();
        $this->climate->description(self::DESCRIPTION);
        $this->climate->arguments->parse();

        $this->exporter = new PlainText();
        $this->exporter->setCommand($this);

        if ($this->climate->arguments->defined('help')) {
            $this->climate->usage();
            exit();
        }

        try {
            /**
             * Parsing Only
             */
            if ($this->climate->arguments->defined('parse-only')) {
                $this->parser = new Parser($this, $this->climate, null);
                $this->parser->parse();
                exit();
            }

            /**
             * Download only
             */
            if ($this->climate->arguments->defined('download-only')) {
                $this->downloader = new Downloader($this, $this->climate);
                if ($this->climate->arguments->defined('season')) {
                    $this->downloader->setSeason($this->climate->arguments->get('season'));
                }
                $this->downloader->download();
            }


        } catch (NHLParserException $e) {
            exit("Parser Error: " . $e->getMessage());
        } catch (NHLDownloaderException $e) {
            exit ("Downloader Error: " . $e->getMessage());
        }
    }

    /**
     * Defines the command line arguments
     *
     * @throws \Exception
     */
    private function createCommandLineArguments()
    {
        $this->climate->arguments->add([
            'help' => [
                'prefix' => 'h',
                'longPrefix' => 'help',
                'description' => 'Displays the help',
                'noValue' => true
            ],
            'parse-only' => [
                'prefix' => 'p',
                'longPrefix' => 'parse-only',
                'description' => 'Parse existing files. Must specify data file location',
                'noValue' => true
            ],
            'download-only' => [
                'prefix' => 'd',
                'longPrefix' => 'download-only',
                'description' => 'Only download the game data files to the data file location and don\'t parse',
                'noValue' => true
            ],
            'files' => [
                'prefix' => 'f',
                'longPrefix' => 'files',
                'description' => 'Directory where data files are/will be stored.'
            ],
            'season' => [
                'prefix' => 's',
                'longPrefix' => 'season',
                'description' => 'Season to download the files for. Use the AAAABBBB format (ie. 20152016)'
            ],
            'verbose' => [
                'prefix' => 'v',
                'longPrefix' => 'verbose',
                'description' => 'Will output different debugging information during the process.',
                'noValue' => true
            ],
            'quick' => [
                'prefix' => 'q',
                'longPrefix' => 'quick',
                'description' => 'Don\'t throttle during the downloading process (NOT recommended)',
                'noValue' => true
            ],
        ]);
    }

    /**
     * Prints a console message only if verbose is on
     *
     * @param $msg
     */
    public function out($msg) {
        if ($this->climate->arguments->defined('verbose')) {
            $this->climate->out($msg);
        }
    }

}