#!/usr/bin/php
<?php

require_once __DIR__ . '/../vendor/autoload.php';

$climate = new \League\CLImate\CLImate();
$command = new NHL\Command($climate);