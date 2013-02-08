<?php
/** @version	2013-02-08-1 */

use Tester\Assert,
	Tester\Dumper;

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
	
	
	public function processAction($sectionName, $actionName, $value)
	{
		return parent::processAction($sectionName, $actionName, $value);
	}
	
	
	public function testResetConfiguration()
	{
		$this->configuration = self::createConfiguration();
	}
}

$adapter = new Adapter;
$adapter->testResetConfiguration();

// NULL action
// <actionName>: #nothing
// or
// <actionName>: NULL
Assert::null($adapter->processAction('section', 'action', NULL));

// Bad action
// <actionName>: 'Hello!'
Assert::throws(function() use ($adapter) {
	$adapter->processAction('sectionName', 'actionName', 'Hello!!!');
}, '\Heymaster\Adapters\AdapterException');


// Empty action
$adapter->processAction('sectionName', 'actionName', array());


// Duplicate name
$adapter->testResetConfiguration();
$adapter->processAction('section', 'my-action', array());

Assert::throws(function () use ($adapter) {
	$adapter->processAction('section', 'my-action', array());
}, 'Heymaster\\Adapters\\AdapterException');


// Normal action
$res = $adapter->processAction('before', 'actionName', array(
	'run' => FALSE,
	'command1' => array(
		'any',
	),
));

$values = $adapter->configuration['sections']['before']['actions']['actionName'];

Assert::false($values[$adapter::KEY_RUNNABLE]);
Assert::same('command1', $values['commands'][0]['name']);
Assert::same('any', $values['commands'][0]['params'][0]);


