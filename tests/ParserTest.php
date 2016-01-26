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
            new \NHL\Entities\Player('#21', 'VAN RIEMSDYK', new \NHL\Entities\Team('TOR')),
            $shot->player
        );

        $this->assertEquals('Wrist', $shot->type);
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
                'number' => '#21',
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
                'number' => '#74',
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
            new \NHL\Entities\Player('#74', 'EMELIN', new \NHL\Entities\Team('MTL')),
            $miss->player
        );

        $this->assertEquals('Wrist', $miss->type);
        $this->assertEquals('Wide of Net', $miss->target);
        $this->assertEquals('Off. Zone', $miss->location);
        $this->assertEquals('62 ft.', $miss->distance);

    }

}
