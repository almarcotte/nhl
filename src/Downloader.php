<?php

namespace NHL;

use League\CLImate\CLImate;
use NHL\Exceptions\NHLDownloaderException;

/**
 * Class Downloader
 * Responsible for downloading data files from NHL.com
 *
 * @package NHL
 */
class Downloader
{

    /** @var array $file_types */
    protected $file_types = [
        'PL', // Play by Play data
        'RO', // Roster
        'TH',
        'TV'
    ];

    /** htmlreports/20152016/PL0200001.HTML */
    const SOURCE_FORMAT = "%s%s%04d.HTM";
    const SOURCE_BASE = "http://www.nhl.com/scores/htmlreports/%s/";

    /** @var array $options */
    protected $options = [
        'season' => '20152016',
        'subseason' => '02',
    ];

    /** @var Command $command */
    protected $command;

    /** @var CLImate $climate */
    protected $climate;

    /**
     * Downloader constructor.
     * @param Command $command
     * @param CLImate $climate
     * @throws NHLDownloaderException
     */
    public function __construct(Command $command, CLImate $climate)
    {
        $this->command = $command;
        $this->climate = $climate;

        if (!$this->climate->arguments->defined('files')) {
            throw new NHLDownloaderException("No path provided to save the files. See --help for info.\n");
        }
    }

    /**
     * Sets the season ID (usually 2 years, ie. 20152016)
     *
     * @param int|string $season
     */
    public function setSeason($season)
    {
        if (mb_strlen($season) !== 8) {
            throw new \InvalidArgumentException("Season should be in a AAAABBBB format, ie. 20152016\n");
        }

        $this->options['season'] = $season;
    }

    public function getSeason()
    {
        return isset($this->options['season']) ? $this->options['season'] : null;
    }

    /**
     * Actually download the files
     */
    public function download()
    {
        /** @var string $url http://.../SEASON/ */
        $url = sprintf(self::SOURCE_BASE, $this->options['season']);

        if (!is_writable($this->climate->arguments->get('files'))) {
            throw new NHLDownloaderException("The specified folder is not writable\n");
        }

        $season_folder = $this->initSeasonFolder();

        foreach(range(1, 100) as $game_number) {
            $file_name = sprintf(self::SOURCE_FORMAT, 'PL', $this->options['subseason'], $game_number);
            $file_local = $season_folder.DIRECTORY_SEPARATOR.$file_name;
            $file_remote = $url.$file_name;

            $this->out("Downloading from " . $file_remote . " to " . $file_local);
            $this->fetchAndSaveFile($file_remote, $file_local);

            if (!$this->climate->arguments->defined('quick') && ($game_number % 5 == 0)) {
                $this->out("Sleeping for 10 seconds...");
                sleep(10);
                $this->out("Resuming...");
            }
        }
    }

    private function initSeasonFolder()
    {
        // Make a folder for the current season, if it doesn't exist
        $season_folder = $this->climate->arguments->get('files').DIRECTORY_SEPARATOR.$this->options['season'];
        if (!file_exists($season_folder)) {
            $this->out("Attempting to create season folder at " . $season_folder);
            mkdir($season_folder);
        }

        return $season_folder;
    }

    /**
     * @param string $url
     * @param string $output
     */
    protected function fetchAndSaveFile($url, $output)
    {
        if (file_exists($output) && !$this->climate->arguments->defined('force')) {
            $this->out("Skipped because it already exists");
            return;
        }

        $file = fopen($url, 'rb');
        if ($file) {
            $new = fopen($output, 'wb');
            if ($new) {
                while (!feof($file)) {
                    fwrite($new, fread($file, 1024 * 8), 1024 * 8);
                }
                fclose($new);
            }
            fclose($file);
        }
    }

    /**
     * Prints a console message only if verbose is on
     *
     * @param $msg
     */
    protected function out($msg) {
        if ($this->climate->arguments->defined('verbose')) {
            $this->climate->out($msg);
        }
    }

}