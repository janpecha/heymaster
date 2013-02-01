<?php
/** @version	2013-02-01-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Config.php';


$config = new Heymaster\Config;
$values = array(
	'root' => 'root',
	'message' => 'Message',
	'output' => FALSE,
);

$config->root = $values['root'];
$config->message = $values['message'];
$config->output = $values['output'];

Assert::same($values, $config->toArray());

