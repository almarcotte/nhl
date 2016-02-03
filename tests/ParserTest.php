<?php

use NHL\Events\Types;

class ParserTest extends PHPUnit_Framework_TestCase
{
    /** @var \League\CLImate\CLImate $climate */
    public static $climate;

    /** @var \NHL\Command $command */
    public static $command;

    /** @var \NHL\Parser $parser */
    public $parser;

    /**
     * Test-wide setup
     */
    public static function setUpBeforeClass()
    {
        self::$climate = new \League\CLImate\CLImate();
        self::$command = new \NHL\Command(self::$climate);
    }

    public function setUp()
    {
        $this->parser = new \NHL\Parser(self::$command);
    }

    public function testParseShot()
    {
        $line = "TOR ONGOAL - #21 VAN RIEMSDYK, Wrist, Off. Zone, 46 ft.";
        $shot = new \NHL\Events\Shot($line);
        $this->assertTrue($shot->parse());

        $this->assertEquals(new \NHL\Entities\Team('TOR'), $shot->team);
        $this->assertEquals(new \NHL\Entities\Player('21', 'VAN RIEMSDYK', new \NHL\Entities\Team('TOR')), $shot->player);

        $this->assertEquals('Wrist', $shot->shotType);
        $this->assertEquals('ONGOAL', $shot->target);
        $this->assertEquals('Off. Zone', $shot->location);
        $this->assertEquals('46', $shot->distance);
    }

    public function testParseMiss()
    {
        $line = "MTL #74 EMELIN, Wrist, Wide of Net, Off. Zone, 62 ft.";
        $miss = new \NHL\Events\Miss($line);
        $this->assertTrue($miss->parse());

        $this->assertEquals(new \NHL\Entities\Team('MTL'), $miss->team);
        $this->assertEquals(new \NHL\Entities\Player('74', 'EMELIN', new \NHL\Entities\Team('MTL')), $miss->player);

        $this->assertEquals('Wrist', $miss->shotType);
        $this->assertEquals('Wide of Net', $miss->target);
        $this->assertEquals('Off. Zone', $miss->location);
        $this->assertEquals('62', $miss->distance);
    }

    public function testParseHit()
    {
        $line = "MTL #79 MARKOV HIT TOR #15 PARENTEAU, Def. Zone";
        $hit = new \NHL\Events\Hit($line);
        $this->assertTrue($hit->parse());

        $this->assertEquals(new \NHL\Entities\Team('MTL'), $hit->teamHitting);
        $this->assertEquals(new \NHL\Entities\Team('TOR'), $hit->teamHit);
        $this->assertEquals(new \NHL\Entities\Player('79', 'MARKOV', new \NHL\Entities\Team('MTL')), $hit->playerHitting);
        $this->assertEquals(new \NHL\Entities\Player('15', 'PARENTEAU', new \NHL\Entities\Team('TOR')), $hit->playerHit);

        $this->assertEquals('Def. Zone', $hit->location);
    }

    public function testGoalParsed()
    {
        $line = "TOR #21 VAN RIEMSDYK(1), Deflected, Def. Zone, 163 ft.Assists: #43 KADRI(1); #3 PHANEUF(1)";
        $goal = new \NHL\Events\Goal($line);
        $this->assertTrue($goal->parse());

        $this->assertEquals('Deflected', $goal->shotType);
        $this->assertEquals('Def. Zone', $goal->location);
        $this->assertEquals('163', $goal->distance);
        $this->assertEquals(new \NHL\Entities\Player('21', 'VAN RIEMSDYK', new \NHL\Entities\Team('TOR')), $goal->player);
        $this->assertEquals([
            new \NHL\Entities\Player('43', 'KADRI', new \NHL\Entities\Team('TOR')),
            new \NHL\Entities\Player('3', 'PHANEUF', new \NHL\Entities\Team('TOR')),
        ],
            $goal->assists
        );
    }

    public function testOtherGoalParsed()
    {
        $line = "NYR #21 STEPAN(1), Tip-In, Off. Zone, 10 ft.Assists: #13 HAYES(1); #20 KREIDER(1)";
        $goal = new \NHL\Events\Goal($line);
        $this->assertTrue($goal->parse());

        $this->assertEquals('Tip-In', $goal->shotType);
        $this->assertEquals('Off. Zone', $goal->location);
        $this->assertEquals('10', $goal->distance);
        $this->assertEquals(new \NHL\Entities\Player('21', 'STEPAN', new \NHL\Entities\Team('NYR')), $goal->player);
        $this->assertEquals([
            new \NHL\Entities\Player('13', 'HAYES', new \NHL\Entities\Team('NYR')),
            new \NHL\Entities\Player('20', 'KREIDER', new \NHL\Entities\Team('NYR')),
        ],
            $goal->assists
        );
    }

    public function testFaceOffParsed()
    {
        $line = "MTL won Off. Zone - MTL #51 DESHARNAIS vs TOR #16 SPALING";
        $faceoff = new \NHL\Events\FaceOff($line);

        $this->assertTrue($faceoff->parse());

        $this->assertEquals(new \NHL\Entities\Team('MTL'), $faceoff->teamWon);
        $this->assertEquals(new \NHL\Entities\Team('MTL'), $faceoff->firstTeam);
        $this->assertEquals(new \NHL\Entities\Team('TOR'), $faceoff->secondTeam);
        $this->assertEquals(new \NHL\Entities\Player('51', 'DESHARNAIS', new \NHL\Entities\Team('MTL')), $faceoff->firstPlayer);
        $this->assertEquals(new \NHL\Entities\Player('16', 'SPALING', new \NHL\Entities\Team('TOR')), $faceoff->secondPlayer);
    }

    public function testBlockParsed()
    {
        $line = "MTL #15 FLEISCHMANN BLOCKED BY TOR #36 HARRINGTON, Wrist, Def. Zone";
        $block = new \NHL\Events\Block($line);
        $this->assertTrue($block->parse());

        $this->assertEquals(new \NHL\Entities\Team('TOR'), $block->teamBlocking);
        $this->assertEquals(new \NHL\Entities\Team('MTL'), $block->teamBlocked);
        $this->assertEquals('Def. Zone', $block->location);
        $this->assertEquals(new \NHL\Entities\Player('15', 'FLEISCHMANN', new \NHL\Entities\Team('MTL')), $block->playerBlocked);
        $this->assertEquals(new \NHL\Entities\Player('36', 'HARRINGTON', new \NHL\Entities\Team('TOR')), $block->playerBlocking);
        $this->assertEquals('Wrist', $block->shotType);
    }

    public function testGiveParsed()
    {
        $line = "MTL@GIVEAWAY - #81 ELLER, Def. Zone";
        $give = new \NHL\Events\Give($line);
        $this->assertTrue($give->parse());


        $this->assertEquals(new \NHL\Entities\Player('81', 'ELLER', new \NHL\Entities\Team('MTL')), $give->player);
        $this->assertEquals(new \NHL\Entities\Team('MTL'), $give->team);
        $this->assertEquals('Def. Zone', $give->location);
    }

    public function testTakeParsed()
    {
        $line = "MTL@TAKEAWAY - #81 ELLER, Def. Zone";
        $give = new \NHL\Events\Take($line);
        $this->assertTrue($give->parse());


        $this->assertEquals(new \NHL\Entities\Player('81', 'ELLER', new \NHL\Entities\Team('MTL')), $give->player);
        $this->assertEquals(new \NHL\Entities\Team('MTL'), $give->team);
        $this->assertEquals('Def. Zone', $give->location);
    }

    public function testPenaltyParsed()
    {
        $line = "TOR #47 KOMAROV@Boarding(2 min), Off. Zone Drawn By: MTL #76 SUBBAN";
        $penalty = new \NHL\Events\Penalty($line);
        $this->assertTrue($penalty->parse());

        $this->assertEquals('Off. Zone', $penalty->location);
        $this->assertEquals(new \NHL\Entities\Player('47', 'KOMAROV', new \NHL\Entities\Team('TOR')), $penalty->player);
        $this->assertEquals(new \NHL\Entities\Player('76', 'SUBBAN', new \NHL\Entities\Team('MTL')), $penalty->drawnPlayer);
        $this->assertEquals(new \NHL\Entities\Team('TOR'), $penalty->team);
        $this->assertEquals(new \NHL\Entities\Team('MTL'), $penalty->drawnTeam);
        $this->assertEquals('Boarding', $penalty->infraction);
        $this->assertEquals('2', $penalty->duration);
    }

    /**
     * During testing with different game files I realised that some penalties had names not previously caught
     * in the regex. This test checks a few of them and makes sure it gets parsed properly
     */
    public function testStrangePenaltyParsed()
    {
        $first = new \NHL\Events\Penalty("S.J #4 DILLON@Hi-sticking(2 min), Def. Zone Drawn By: L.A #10 EHRHOFF");
        $this->assertTrue($first->parse());
        $this->assertEquals('Def. Zone', $first->location);
        $this->assertEquals(new \NHL\Entities\Player('4', 'DILLON', new \NHL\Entities\Team('S.J')), $first->player);
        $this->assertEquals(new \NHL\Entities\Player('10', 'EHRHOFF', new \NHL\Entities\Team('L.A')), $first->drawnPlayer);
        $this->assertEquals(new \NHL\Entities\Team('S.J'), $first->team);
        $this->assertEquals(new \NHL\Entities\Team('L.A'), $first->drawnTeam);
        $this->assertEquals('Hi-sticking', $first->infraction);
        $this->assertEquals('2', $first->duration);

        // Penalty not drawn by anybody
        $second = new \NHL\Events\Penalty("L.A #21 SHORE@Closing hand on puck(2 min), Def. Zone");
        $this->assertTrue($second->parse());
        $this->assertEquals('Def. Zone', $second->location);
        $this->assertEquals(new \NHL\Entities\Player('21', 'SHORE', new \NHL\Entities\Team('L.A')), $second->player);
        $this->assertEquals(new \NHL\Entities\Team('L.A'), $second->team);
        $this->assertEquals('Closing hand on puck', $second->infraction);
        $this->assertEquals('2', $second->duration);
        $this->assertNull($second->drawnPlayer);
        $this->assertNull($second->drawnTeam);

        // Major penalty
        $third = new \NHL\Events\Penalty("S.J #89 GOODROW@Fighting (maj)(5 min), Off. Zone Drawn By: L.A #15 ANDREOFF");
        $this->assertTrue($third->parse());
        $this->assertEquals('Off. Zone', $third->location);
        $this->assertEquals(new \NHL\Entities\Player('89', 'GOODROW', new \NHL\Entities\Team('S.J')), $third->player);
        $this->assertEquals(new \NHL\Entities\Player('15', 'ANDREOFF', new \NHL\Entities\Team('L.A')), $third->drawnPlayer);
        $this->assertEquals(new \NHL\Entities\Team('S.J'), $third->team);
        $this->assertEquals(new \NHL\Entities\Team('L.A'), $third->drawnTeam);
        $this->assertEquals('Fighting (maj)', $third->infraction);
        $this->assertEquals('5', $third->duration);
    }

    /**
     * Some penalties are served by another player. Make sure this is handled properly
     */
    public function testServedByPenalty()
    {
        $first = new \NHL\Events\Penalty("L.A #2 GREENE@Interference(2 min) Served By: #21 SHORE, Def. Zone Drawn By: S.J #89 GOODROW");
        $this->assertTrue($first->parse());
        $this->assertEquals('Def. Zone', $first->location);
        $this->assertEquals(new \NHL\Entities\Player('2', 'GREENE', new \NHL\Entities\Team('L.A')), $first->player);
        $this->assertEquals(new \NHL\Entities\Player('89', 'GOODROW', new \NHL\Entities\Team('S.J')), $first->drawnPlayer);
        $this->assertEquals(new \NHL\Entities\Team('L.A'), $first->team);
        $this->assertEquals(new \NHL\Entities\Team('S.J'), $first->drawnTeam);
        $this->assertEquals(new \NHL\Entities\Player('21', 'SHORE', new \NHL\Entities\Team('L.A')), $first->servedByPlayer);
        $this->assertEquals('Interference', $first->infraction);
        $this->assertEquals('2', $first->duration);

        $second = new \NHL\Events\Penalty("L.A #17 LUCIC@Match penalty(10 min) Served By: #21 SHORE, Neu. Zone Drawn By: S.J #39 COUTURE");
        $this->assertTrue($second->parse());
        $this->assertEquals('Neu. Zone', $second->location);
        $this->assertEquals(new \NHL\Entities\Player('17', 'LUCIC', new \NHL\Entities\Team('L.A')), $second->player);
        $this->assertEquals(new \NHL\Entities\Player('39', 'COUTURE', new \NHL\Entities\Team('S.J')), $second->drawnPlayer);
        $this->assertEquals(new \NHL\Entities\Team('L.A'), $second->team);
        $this->assertEquals(new \NHL\Entities\Team('S.J'), $second->drawnTeam);
        $this->assertEquals(new \NHL\Entities\Player('21', 'SHORE', new \NHL\Entities\Team('L.A')), $second->servedByPlayer);
        $this->assertEquals('Match penalty', $second->infraction);
        $this->assertEquals('10', $second->duration);
    }

    public function testPeriodEndParsed()
    {
        $line = "Period End- Local time: 8:00 EDT";
        $period = new \NHL\Events\Period($line);
        $this->assertTrue($period->parse());

        $this->assertEquals(Types::PERIODEND, $period->eventType);
        $this->assertEquals('8:00', $period->time);
        $this->assertEquals('EDT', $period->timezone);
    }

    public function testPeriodStartParsed()
    {
        $line = "Period Start- Local time: 8:19 EDT";
        $period = new \NHL\Events\Period($line);
        $this->assertTrue($period->parse());

        $this->assertEquals(Types::PERIODSTART, $period->eventType);
        $this->assertEquals('8:19', $period->time);
        $this->assertEquals('EDT', $period->timezone);
    }

    public function testGameEndParsed()
    {
        $line = "Game End- Local time: 9:56 EDT";
        $period = new \NHL\Events\Period($line);
        $this->assertTrue($period->parse());

        $this->assertEquals(Types::GAMEEND, $period->eventType);
        $this->assertEquals('9:56', $period->time);
        $this->assertEquals('EDT', $period->timezone);
    }

    public function testStopParsed()
    {
        $line = "OFFSIDE";
        $stop = new \NHL\Events\Stop($line);
        $this->assertTrue($stop->parse());

        $this->assertEquals('OFFSIDE', $stop->reason);
        $this->assertNull($stop->other);
    }

    public function testStopWithOtherReason()
    {
        $line = "PUCK IN BENCHES,TV TIMEOUT";
        $stop = new \NHL\Events\Stop($line);
        $this->assertTrue($stop->parse());

        $this->assertEquals('PUCK IN BENCHES', $stop->reason);
        $this->assertEquals('TV TIMEOUT', $stop->other);
    }

}
