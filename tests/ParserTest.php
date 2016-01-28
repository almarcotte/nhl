<?php

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
        $this->parser = new \NHL\Parser(self::$command, self::$climate);
    }

    public function testParseShot()
    {
        $line = "TOR ONGOAL - #21 VAN RIEMSDYK, Wrist, Off. Zone, 46 ft.";
        $shot = new \NHL\Events\Shot($line);
        $shot->parse();

        $this->assertEquals(
            new \NHL\Entities\Team('TOR'),
            $shot->team
        );
        $this->assertEquals(
            new \NHL\Entities\Player('21', 'VAN RIEMSDYK', new \NHL\Entities\Team('TOR')),
            $shot->player
        );

        $this->assertEquals('Wrist', $shot->shotType);
        $this->assertEquals('ONGOAL', $shot->target);
        $this->assertEquals('Off. Zone', $shot->location);
        $this->assertEquals('46 ft.', $shot->distance);
    }

    public function testParseShotAndReturnArray()
    {
        $line = "TOR ONGOAL - #21 VAN RIEMSDYK, Wrist, Off. Zone, 46 ft.";
        $shot = new \NHL\Events\Shot($line);
        $result = $shot->toArray();

        $this->assertEquals(
            [
                'team' => 'TOR',
                'target' => 'ONGOAL',
                'number' => '21',
                'player' => 'VAN RIEMSDYK',
                'type' => 'Wrist',
                'location' => 'Off. Zone',
                'distance' => '46 ft.'
            ],
            $result);
    }

    public function testParseMissAndReturnArray()
    {
        $line = "MTL #74 EMELIN, Wrist, Wide of Net, Off. Zone, 62 ft.";
        $miss = new \NHL\Events\Miss($line);
        $this->assertEquals(
            [
                'team' => 'MTL',
                'target' => 'Wide of Net',
                'number' => '74',
                'player' => 'EMELIN',
                'type' => 'Wrist',
                'location' => 'Off. Zone',
                'distance' => '62 ft.'
            ],
            $miss->toArray()
        );
    }

    public function testParseMiss()
    {
        $line = "MTL #74 EMELIN, Wrist, Wide of Net, Off. Zone, 62 ft.";
        $miss = new \NHL\Events\Miss($line);
        $miss->parse();

        $this->assertEquals(
            new \NHL\Entities\Team('MTL'),
            $miss->team
        );
        $this->assertEquals(
            new \NHL\Entities\Player('74', 'EMELIN', new \NHL\Entities\Team('MTL')),
            $miss->player
        );

        $this->assertEquals('Wrist', $miss->shotType);
        $this->assertEquals('Wide of Net', $miss->target);
        $this->assertEquals('Off. Zone', $miss->location);
        $this->assertEquals('62 ft.', $miss->distance);
    }

    public function testParseHit()
    {
        $line = "MTL #79 MARKOV HIT TOR #15 PARENTEAU, Def. Zone";
        $hit = new \NHL\Events\Hit($line);
        $hit->parse();

        $this->assertEquals(
            new \NHL\Entities\Team('MTL'),
            $hit->team1
        );

        $this->assertEquals(
            new \NHL\Entities\Team('TOR'),
            $hit->team2
        );

        $this->assertEquals(
            new \NHL\Entities\Player('79', 'MARKOV', new \NHL\Entities\Team('MTL')),
            $hit->player1
        );

        $this->assertEquals(
            new \NHL\Entities\Player('15', 'PARENTEAU', new \NHL\Entities\Team('TOR')),
            $hit->player2
        );

        $this->assertEquals('Def. Zone', $hit->location);
    }

    public function testParseHitAsArray()
    {
        $line = "MTL #79 MARKOV HIT TOR #15 PARENTEAU, Def. Zone";
        $hit = new \NHL\Events\Hit($line);
        $array = $hit->toArray();

        $this->assertEquals(
            [
                'team1' => 'MTL',
                'team2' => 'TOR',
                'player1' => 'MARKOV',
                'player2' => 'PARENTEAU',
                'number1' => '79',
                'number2' => '15',
                'location' => 'Def. Zone'
            ],
            $array);
    }

    public function testGoalAssistAsArray()
    {
        $line = "TOR #21 VAN RIEMSDYK(1), Deflected, Def. Zone, 163 ft.Assists: #43 KADRI(1); #3 PHANEUF(1)";
        $goal = new \NHL\Events\Goal($line);
        $data = $goal->toArray();

        $this->assertEquals([
            [
                'number' => '43',
                'name' => 'KADRI'
            ],
            [
                'number' => '3',
                'name' => 'PHANEUF'
            ]
        ], $data['assists']);
    }

    public function testGoalInfoAsArray()
    {
        $line = "TOR #21 VAN RIEMSDYK(1), Deflected, Def. Zone, 163 ft.Assists: #43 KADRI(1); #3 PHANEUF(1)";
        $goal = new \NHL\Events\Goal($line);
        $data = $goal->toArray();

        $this->assertEquals(
            [
                'team' => 'TOR',
                'number' => '21',
                'name' => 'VAN RIEMSDYK',
                'type' => 'Deflected',
                'location' => 'Def. Zone',
                'distance' => '163'
            ],
            $data['goal']
        );
    }

    public function testGoalParsed()
    {
        $line = "TOR #21 VAN RIEMSDYK(1), Deflected, Def. Zone, 163 ft.Assists: #43 KADRI(1); #3 PHANEUF(1)";
        $goal = new \NHL\Events\Goal($line);
        $goal->parse();

        $this->assertEquals('Deflected', $goal->shotType);
        $this->assertEquals('Def. Zone', $goal->location);
        $this->assertEquals('163', $goal->distance);
        $this->assertEquals(
            new \NHL\Entities\Player('21', 'VAN RIEMSDYK', new \NHL\Entities\Team('TOR')),
            $goal->player
        );
        $this->assertEquals([
            new \NHL\Entities\Player('43', 'KADRI', new \NHL\Entities\Team('TOR')),
            new \NHL\Entities\Player('3', 'PHANEUF', new \NHL\Entities\Team('TOR')),
        ],
            $goal->assists
        );
    }

    public function testFaceOffAsArray()
    {
        $line = "MTL won Off. Zone - MTL #51 DESHARNAIS vs TOR #16 SPALING";
        $faceoff = new \NHL\Events\FaceOff($line);

        $this->assertEquals(
            [
                'team_won' => 'MTL',
                'location' => 'Off. Zone',
                'home_team' => 'MTL',
                'home_number' => '51',
                'home_player' => 'DESHARNAIS',
                'away_team' => 'TOR',
                'away_number' => '16',
                'away_player' => 'SPALING'
            ],
            $faceoff->toArray()
        );
    }

    public function testFaceOffParsed()
    {
        $line = "MTL won Off. Zone - MTL #51 DESHARNAIS vs TOR #16 SPALING";
        $faceoff = new \NHL\Events\FaceOff($line);

        $this->assertTrue($faceoff->parse());

        $this->assertEquals(
            new \NHL\Entities\Team('MTL'),
            $faceoff->team_won
        );

        $this->assertEquals(
            new \NHL\Entities\Team('MTL'),
            $faceoff->home_team
        );

        $this->assertEquals(
            new \NHL\Entities\Team('TOR'),
            $faceoff->away_team
        );

        $this->assertEquals(
            new \NHL\Entities\Player('51', 'DESHARNAIS', new \NHL\Entities\Team('MTL')),
            $faceoff->home_player
        );

        $this->assertEquals(
            new \NHL\Entities\Player('16', 'SPALING', new \NHL\Entities\Team('TOR')),
            $faceoff->away_player
        );

    }


}
