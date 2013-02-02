<?php
/** @version	2013-02-02-2 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Configs/FileConfig.php';


$config = new Heymaster\Configs\FileConfig;
$values = array(
	'root' => NULL,
	'message' => FALSE,
	'output' => NULL,
	'inherit' => FALSE,
	'builder' => 'git',
);

$config->set('inherit', 'String value');
$array = $config->toArray();
Assert::true($array['inherit']);

$config->set('inherit', FALSE);
$config->set('builder', 'git');
Assert::same($values, $config->toArray());

