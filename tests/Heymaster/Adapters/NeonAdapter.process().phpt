<?php
/** @version	2013-02-07-1 */

use Tester\Assert;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';

class Adapter extends Heymaster\Adapters\NeonAdapter
{
	public function getConfiguration()
	{
		return $this->configuration;
	}
	
	
	public function process(array $array)
	{
		$this->configuration = self::createConfiguration();
		return parent::process($array);
	}
	
	
	public static function createConfiguration()
	{
		return parent::createConfiguration();
	}
}

$adapter = new Adapter;

// Config
$adapter->process(array(
	'output' => FALSE,
));

Assert::false($adapter->configuration['output']);


// Invalid content of section
Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => 10,
	));
}, 'Heymaster\\Adapters\\AdapterException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => FALSE,
	));
}, 'Heymaster\\Adapters\\AdapterException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'before' => 'Hello!',
	));
}, 'Heymaster\\Adapters\\AdapterException');


Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => 10,
	));
}, 'Heymaster\\Adapters\\AdapterException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => FALSE,
	));
}, 'Heymaster\\Adapters\\AdapterException');

Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'after' => 'Hello!',
	));
}, 'Heymaster\\Adapters\\AdapterException');


// Unknow config option
Assert::throws(function () use ($adapter) {
	$adapter->process(array(
		'unknow-config-option' => 'Hello!',
	));
}, 'Heymaster\\Adapters\\AdapterException');


// Parameters
$adapter->process(array(
	'parameters' => array(
		'git.branch' => 'my-branch',
	),
));

Assert::same('my-branch', $adapter->configuration['parameters']['git']['branch']);

// NULL section
$adapter->process(array(
	'before' => NULL,
	'after' => NULL,
));

$configuration = $adapter::createConfiguration();
$configuration['sections'] = array(
	'before' => NULL,
	'after' => NULL,
);
Assert::same($configuration, $adapter->configuration);


