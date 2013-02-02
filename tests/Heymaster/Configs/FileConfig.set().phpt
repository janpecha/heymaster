<?php
/** @version	2013-02-02-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Configs/FileConfig.php';


$config = new Heymaster\Configs\FileConfig;

// inherit (bool value)
Assert::false($config->inherit);

$config->set('inherit', 'String value');
Assert::true($config->inherit);

$config->set('inherit', FALSE);
Assert::false($config->inherit);

