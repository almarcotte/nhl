<?php

namespace NHL;

use League\CLImate\CLImate;
use NHL\Exceptions\DownloaderException;
use NHL\Exceptions\ExporterException;
use NHL\Exceptions\ParserException;

/**
 * Class Command
 * Represents the command line interface, main entry point of the entire app
 *
 * @package NHL
 */
class Command
{
    const DESCRIPTION = "An NHL.com data file processor";

    const DEFAULT_CONFIG = __DIR__ . '/../config.ini';

    /** @var CLImate $climate */
    public $climate;

    /** @var Downloader $downloader */
    public $downloader;

    /** @var Contracts\Exporter $exporter */
    public $exporter;

    /** @var Config $config */
    public $config;

    /** @var Parser $parser */
    public $parser;

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

        if ($this->climate->arguments->defined('help')) {
            $this->climate->usage();
            exit();
        }

        if ($this->climate->arguments->defined('list')) {
            $this->showList();
            exit();
        }

        if ($this->climate->arguments->defined('config')) {
            $config_file = $this->climate->arguments->get('config');
            if (!file_exists($config_file)) {
                die("Couldn't read configuration file. Make sure the path is correct.");
            }
        } else {
            $config_file = self::DEFAULT_CONFIG;
        }

        $this->prepareConfig($config_file);
    }

    public function run()
    {
        try {
            $exporter = $this->config->get('export', 'exporter') ? $this->config->get('export', 'exporter') : 'stdout';
            $this->exporter = ExporterFactory::make($exporter, $this);
            $this->out("Using Exporter: $exporter");

            /**
             * Parsing or Downloading only
             */
            if ($this->config->get('general', 'parse-only')) {
                $this->parser = new Parser($this);
                $this->parser->parse();
                exit();
            } else if ($this->config->get('general', 'download-only')) {
                $this->downloader->download();
                exit();
            }

            /**
             * Otherwise we download and parse
             */
            $this->downloader = new Downloader($this);
            $this->downloader->download();

            $this->parser = new Parser($this);
            $this->parser->parse();

        } catch (ParserException $e) {
            exit("Parser Error: " . $e->getMessage());
        } catch (DownloaderException $e) {
            exit ("Downloader Error: " . $e->getMessage());
        } catch (ExporterException $e) {
            exit ("Exporter Error: " . $e->getMessage());
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
            'config' => [
                'prefix' => 'c',
                'longPrefix' => 'config',
                'description' => 'Provide a configuration file instead of using the command line arguments'
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
            'exporter' => [
                'prefix' => 'e',
                'longPrefix' => 'exporter',
                'description' => 'Specify which data exporter to use. See --list exporters for more info.',
            ],
            'list' => [
                'longPrefix' => 'list',
                'description' => 'Lists available implementations for a given type. Available: exporters'
            ]
        ]);
    }

    /**
     * Prints a console message only if verbose is on
     *
     * @param $msg
     */
    public function out($msg)
    {
        if ($this->config->get('general', 'verbose')) {
            $this->climate->out($msg);
        }
    }

    /**
     * Outputs all the available implementation for the given type (such as Exporters)
     * Unfortunately most of these have to be hardcoded because of autoloading (otherwise we'd use reflection)
     */
    private function showList()
    {
        $type = $this->climate->arguments->get('list');
        $data = [];
        if ($type == 'exporters') {
            $data = [
                [
                    'name' => 'Void',
                    'description' => 'Does not output anything even if parsing is done. Pretty much useless.'
                ],
                [
                    'name' => '<bold>StdOut</bold>',
                    'description' => '<bold>Prints all the data, formatted in a human-readable format, to the standard output.</bold>'
                ],
                [
                    'name' => 'File',
                    'description' => 'Writes each game in its own file using the same human-readable format as StdOut'
                ],
                [
                    'name' => 'CSV',
                    'description' => 'Writes each game to its own file in the comma-separated-values format'
                ],
                [
                    'name' => 'MySQL',
                    'description' => 'Creates the appropriate tables and inserts the data in a MySQL database'
                ],
                [
                    'name' => 'MySQL-Dump',
                    'description' => 'Creates a MySQL .sql file with all the required statements to create a database'
                ],
            ];
        }

        if (!empty($data)) {
            $this->climate->table($data);
        }
    }

    /**
     * Set up the configuration by loading the config file and merging with command line options
     * @param $file
     */
    public function prepareConfig($file)
    {
        $this->config = new Config($file);

        $conf = $this->config->getAllFields();

        foreach ($conf as $section => $options) {
            foreach ($options as $option) {
                if ($this->climate->arguments->defined($option)) {
                    $this->config->set($section, $option, $this->climate->arguments->get($option));
                }
            }
        }
    }

}