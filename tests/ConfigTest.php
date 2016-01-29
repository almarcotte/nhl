<?php

class ConfigTest extends PHPUnit_Framework_TestCase
{
    /** @var \NHL\Config $config */
    public $config;

    public function setUp()
    {
        $this->config = new \NHL\Config(__DIR__ . '/test.ini');
        var_dump($this->config);
    }

    public function testReadOption()
    {
        $this->assertEquals('20152016', $this->config->general->season);
    }

}
