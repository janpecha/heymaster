<?php
/** @version	2013-02-08-1 */

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
	
	public function processCommand($sectionName, $actionName, $commandName, $value)
	{
		return parent::processCommand($sectionName, $actionName, $commandName, $value);
	}
	
	public function testResetConfiguration()
	{
		$this->configuration = self::createConfiguration();
	}
}

$adapter = new Adapter;
$adapter->testResetConfiguration();

// Valid command
$command = $adapter->processCommand('section', 'action', 'my-command', array(
	'arg0', 'arg1',
));

Assert::same('my-command', $command['name']);
Assert::same('arg0', $command['params'][0]);
Assert::same('arg1', $command['params'][1]);
Assert::false($command['description']);


$command = $adapter->processCommand('section', 'action', 'command with a description', array(
	'arg0' => 'first',
	'arg1' => 'second',
));

Assert::same('command', $command['name']);
Assert::same('first', $command['params']['arg0']);
Assert::same('second', $command['params']['arg1']);
Assert::same('with a description', $command['description']);


// Simple command syntax
$command = $adapter->processCommand('section', 'action', 0, 'my-command');
Assert::same('my-command', $command['name']);
Assert::same(array(), $command['params']);
Assert::false($command['description']);


// NULL value
$command = $adapter->processCommand('section', 'action', 'my-command', NULL);
Assert::same('my-command', $command['name']);
Assert::null($command['params']);
Assert::false($command['description']);


// Bad value
Assert::throws(function () use ($adapter) {
	$adapter->processCommand('section', 'action', 'my-command', 'Hello!');
}, 'Heymaster\\Adapters\\AdapterException');

