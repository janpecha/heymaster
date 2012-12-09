<?php
/** @version	2012-12-09-1 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Command.php';
require __DIR__ . '/../../../src/Action.php';
require __DIR__ . '/../../../src/Section.php';
require __DIR__ . '/../../../src/Configs/FileConfig.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';

class Adapter extends Heymaster\Adapters\NeonAdapter
{
	public function process(array $array)
	{
		return parent::process($array);
	}
}

$adapter = new Adapter;

// Config
$res = $adapter->process(array(
	'output' => FALSE,
));

Assert::false($res['config']->output);


// Invalid content of section
Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => 10,
	));
}, 'UnexpectedValueException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => FALSE,
	));
}, 'UnexpectedValueException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => 'Hello!',
	));
}, 'UnexpectedValueException');


Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => 10,
	));
}, 'UnexpectedValueException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => FALSE,
	));
}, 'UnexpectedValueException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => 'Hello!',
	));
}, 'UnexpectedValueException');

