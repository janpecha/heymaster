<?php
/** @version	2013-02-02-1 */
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
);

$config->set('inherit', 'String value');
$array = $config->toArray();
Assert::true($array['inherit']);

$config->set('inherit', FALSE);
Assert::same($values, $config->toArray());

