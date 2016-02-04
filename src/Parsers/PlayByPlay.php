<?php

namespace NHL\Parsers;

use NHL\Entities\Game;
use NHL\Entities\Team;
use NHL\Event;
use NHL\Events\Types;
use NHL\Contracts\Parser;
use NHL\Exceptions\ParserException;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Dom\AbstractNode;

/**
 * Class PlayByPlay
 *
 * @package NHL\Parsers
 */
class PlayByPlay extends Parser
{
    protected $filePattern = "/PL.+\\.HTM/";

    /** @var string $name */
    public $name = "Play By Play";

    /**
     * Parses files
     *
     * @return bool
     * @throws ParserException
     */
    public function parse()
    {
        ini_set('memory_limit', -1);

        $this->prepareFiles();
        $files = $this->getAllFileNames();
        foreach ($files as $filename) {
            $game = $this->processFile($filename);

            $this->command->out("Exporting...");
            $this->command->exporter->setGame($game);
            $this->command->exporter->export();
            $this->command->out("Done!");
        }

        return true;
    }

    /**
     * Parses the given file and returns a Game object
     *
     * @param string $filename
     *
     * @return Game
     */
    protected function processFile($filename)
    {
        $this->command->out("Processing ".$filename);

        // Create a game object with home/away teams and other info
        $game = $this->createGameWithInfo($filename);

        $dom = new Dom();
        $dom->loadFromFile($filename);

        $lines = [];
        /** @var AbstractNode $tr */
        foreach ($dom->find('tr.evenColor') as $tr) {
            $lineContent = [];
            $lineCount = 0;
            /** @var AbstractNode $td */
            foreach ($tr->getChildren() as $td) {
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

        // Add each event line to the game log
        foreach ($lines as $line) {
            if ($event = $this->createParsedEvent($line)) {
                $game->addEvent($event);
            }
        }

        return $game;
    }

    /**
     * @param $line
     *
     * @return Event|bool
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

            return $event;
        } else {
            return false;
        }
    }


    /**
     * @param string $filename
     *
     * @return Game
     */
    private function createGameWithInfo($filename)
    {
        // Temporarily disable xml errors since the file we're parsing is a bit of a mess
        libxml_use_internal_errors(true);

        $doc = new \DOMDocument();
        $doc->loadHTMLFile($filename);

        // These contain all the info we need with a whole lot of extra whitespace to remove
        $v_text = preg_replace("/[^A-Za-z\\d]/", "", $doc->getElementById('Visitor')->textContent);
        $h_text = preg_replace("/[^A-Za-z\\d]/", "", $doc->getElementById('Home')->textContent);

        // Grab the home / away teams and scores
        if (preg_match_all(Game::RX_SCORETEAMS, $v_text, $matches_visitor)) {
            $away = new Team($matches_visitor[2][0]);
            $away_score = $matches_visitor[1][0];
        } else {
            // DEBUG: Remove me later OK?
            var_dump($v_text);
            die();

            return false;
        }

        if (preg_match_all(Game::RX_SCORETEAMS, $h_text, $matches_home)) {
            $home = new Team($matches_home[2][0]);
            $home_score = $matches_home[1][0];
        } else {
            // DEBUG: Remove me later OK?
            var_dump($h_text);
            die();

            return false;
        }

        // Generate the game ID based off of the filename
        $parts = explode(DIRECTORY_SEPARATOR, $filename);
        $season = $parts[count($parts) - 2];
        $game_number = str_replace('.HTM', '', end($parts));
        $game_id = $season.$game_number;

        $game = new Game($game_id);
        $game->setHomeTeam($home);
        $game->setAwayTeam($away);
        $game->setFinalScore($home_score, $away_score);
        $game->setSeason($season);

        // Get the attendence, start/end times and location
        /** @var \DOMNode $childNode */
        foreach ($doc->getElementById('GameInfo')->childNodes as $childNode) {
            $value = trim(preg_replace('!\s+!', ' ', $childNode->textContent));
            $value = preg_replace('/[^a-zA-Z0-9\s\:]/', '', $value);
            if (mb_strlen($value) <= 2) {
                continue;
            }

            if (preg_match(Game::RX_DATE, $value, $matches)) {
                $game->date = $matches[1];
            } else if (preg_match(Game::RX_ATTEND, $value, $matches)) {
                $game->attendance = (int)$matches[1];
                $game->venue = $matches[2];
            } else if (preg_match_all(Game::RX_ENDSTART, $value, $matches)) {
                $game->startTime = $matches[1][0];
                $game->startTimeZone = $matches[2][0];
                $game->endTime = $matches[1][1];
                $game->endTimeZone = $matches[2][1];
            } else if ($value == "Final") {
                $game->wentOverTime = false;
            }
        }

        libxml_use_internal_errors(false);

        return $game;
    }
}