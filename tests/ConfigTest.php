<?php
use NHL\Factories\ExporterFactory;

/**
 * Class ConfigTest
 *
 * Tests for Config and Exporter Settings
 */
class ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var \League\CLImate\CLImate $climate */
    public static $climate;

    /** @var \NHL\Command */
    public static $command;

    public static function setUpBeforeClass()
    {
        self::$climate = new \League\CLImate\CLImate();
        self::$command = new \NHL\Command(self::$climate);
        self::$command->config = new \NHL\Config(__DIR__.DIRECTORY_SEPARATOR.'test.ini');
    }

    public function testConfigFile()
    {
        $this->assertInstanceOf('\\NHL\\Config', self::$command->config);
    }

    /**
     * @param string $section
     * @param string $option
     * @param mixed  $expected
     *
     * @dataProvider optionProvider
     */
    public function testGetOption($section, $option, $expected)
    {
        $this->assertEquals($expected, self::$command->config->get($section, $option));
    }

    public function optionProvider()
    {
        return [
            ['general', 'verbose', 1],
            ['general', 'season', '20152016'],
            ['export', 'exporter', 'csv'],
            ['csv', 'nameFormat', '%GAMEID%'], // Depending on another section
            ['csv', 'extension', 'csv'], // Overwrite same setting from FILE
        ];
    }

    public function testFileExporterConfig()
    {
        /** @var \NHL\Exporters\File $fileExporter */
        $fileExporter = ExporterFactory::make('file', self::$command);

        foreach(['path', 'oneFilePerGame', 'bySeason', 'nameFormat'] as $option) {
            $this->assertTrue($fileExporter->hasOption($option));
        }
    }

    public function testExporterConfigWithDependency()
    {
        /** @var \NHL\Exporters\CSV $csvExporter */
        $csvExporter = ExporterFactory::make('csv', self::$command);

        $this->assertEquals(1, $csvExporter->getOption('bySeason'));
        $this->assertEquals('csv', $csvExporter->getOption('extension')); // Should be CSV, not null
        $this->assertEquals('a,b', $csvExporter->getOption('ignoreColumns'));
        $this->assertEquals('output/', $csvExporter->getOption('path')); // Comes from [export] settings
    }

}
