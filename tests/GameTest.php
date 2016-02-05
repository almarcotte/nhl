<?php

/**
 * Class GameTest
 */
class GameTest extends PHPUnit_Framework_TestCase
{
    /** @var bool $backupGlobals */
    public $backupGlobals = false;

    public function testConstructionWithParameters()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $this->assertEquals('20152016', $game->season);
        $this->assertEquals("20152016PL020005", $game->id);
        $this->assertEquals("0005", $game->shortID);
    }

    public function testSeason()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $this->assertEquals('20152016', $game->season);

        $game->setSeason('20092010');
        $this->assertEquals('20092010', $game->season);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp "Season must be 2 consecutive years, ex. 20152016, \d+ given"
     */
    public function testSeasonIsNotConsecutiveYears()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $this->assertEquals('20152016', $game->season);

        $game->setSeason("20092015");
        $game->setSeason("20072009");
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessageRegExp "Season must be 2 consecutive years, ex. 20152016, \d+ given"
     */
    public function testSeasonIsInWrongOrder()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $this->assertEquals('20152016', $game->season);
        $game->setSeason("20102009");
    }

    public function testScoreAndTeams()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $game->setAwayTeam(new \NHL\Entities\Team("TOR"));
        $game->setHomeTeam(new \NHL\Entities\Team("MTL"));

        $this->assertEquals(new \NHL\Entities\Team("TOR"), $game->away);
        $this->assertEquals(new \NHL\Entities\Team("MTL"), $game->home);

        $game->setFinalScore(5, 3);
        $this->assertEquals(5, $game->homeScore);
        $this->assertEquals(3, $game->awayScore);

        $this->assertEquals("TOR (3) at MTL (5)", (string)$game);
    }

    public function testEvents()
    {
        $game = new \NHL\Entities\Game("20152016PL020005");
        $game->setAwayTeam(new \NHL\Entities\Team("TOR"));
        $game->setHomeTeam(new \NHL\Entities\Team("MTL"));

        $goal = new \NHL\Events\Goal("TOR #21 VAN RIEMSDYK(1), Deflected, Def. Zone, 163 ft.Assists: #43 KADRI(1); #3 PHANEUF(1)");
        $goal->parse();

        $game->addEvent($goal);
        $events = $game->getEvents();

        $this->assertEquals($goal, $events[0]);
    }
}
