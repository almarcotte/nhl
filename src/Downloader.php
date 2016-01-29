<?php

namespace NHL;

use NHL\Exceptions\DownloaderException;

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

    /** sleep for 10 seconds every 10 files */
    const SLEEP_TIME = 10;
    const SLEEP_EVERY = 10;

    /** @var array $options */
    protected $options = [
        'season'    => '20152016',
        'subseason' => '02',
    ];

    /** @var Command $command */
    protected $command;

    /** @var int $downloaded */
    private $downloaded;

    /**
     * Downloader constructor.
     *
     * @param Command $command
     *
     * @throws DownloaderException
     */
    public function __construct(Command $command)
    {
        $this->command = $command;

        if (!$this->command->config->get('general', 'files')) {
            throw new DownloaderException("No path provided to save the files. See --help for info.\n");
        }
    }

    /**
     * Actually download the files
     */
    public function download()
    {
        /** @var string $url http://.../SEASON/ */
        $url = sprintf(self::SOURCE_BASE, $this->options['season']);

        if (!is_writable($this->command->config->get('paths', 'files'))) {
            throw new DownloaderException("The specified folder is not writable\n");
        }

        $season_folder = $this->initSeasonFolder();

        $this->downloaded = 0;
        foreach (range(1, 100) as $game_number) {
            $file_name = sprintf(self::SOURCE_FORMAT, 'PL', $this->options['subseason'], $game_number);
            $file_local = $season_folder.DIRECTORY_SEPARATOR.$file_name;
            $file_remote = $url.$file_name;

            $this->command->out("Downloading from ".$file_remote." to ".$file_local);
            $this->fetchAndSaveFile($file_remote, $file_local);

            if (!$this->command->climate->arguments->defined('quick') && ($this->downloaded % self::SLEEP_EVERY == 0)) {
                $this->command->out("Sleeping for ".self::SLEEP_TIME." seconds...");
                sleep(self::SLEEP_TIME);
                $this->command->out("Resuming...");
            }
        }
        $this->command->out("Downloaded {$this->downloaded} files after trying {$game_number}");
    }

    private function initSeasonFolder()
    {
        // Make a folder for the current season, if it doesn't exist
        $season_folder = $this->command->config->get('general', 'files') . DIRECTORY_SEPARATOR . $this->options['season'];
        if (!file_exists($season_folder)) {
            $this->command->out("Attempting to create season folder at ".$season_folder);
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
        if (file_exists($output) && !$this->command->climate->arguments->defined('force')) {
            $this->command->out("Skipped because it already exists");

            return;
        }
        $this->downloaded++;

        file_put_contents($output, fopen($url, 'r'));
    }

}