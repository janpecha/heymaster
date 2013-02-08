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
	
	public function processSection($key, $value)
	{
		return parent::processSection($key, $value);
	}
	
	public function testResetConfiguration()
	{
		$this->configuration = self::createConfiguration();
	}
}

$adapter = new Adapter;
$adapter->testResetConfiguration();

// NULL section
// <section-name>: #nothing
// or
// <section-name>: NULL
Assert::null($adapter->processSection('section', NULL));


// Invalid section
// <section-name>: Hello
Assert::throws(function () use ($adapter) {
	$adapter->processSection('section', 'Hello');
}, 'Heymaster\\Adapters\\AdapterException');


// Valid section
$adapter->testResetConfiguration();
$adapter->processSection('before', array(
	'root' => 'my-root',
	'message' => 'My message',
	'output' => TRUE,
	
	'action-name' => NULL,
	'next-action' => NULL,
));

$values = $adapter->configuration['sections']['before'];
Assert::same('my-root', $values['root']);
Assert::same('My message', $values['message']);
Assert::true($values['output']);
Assert::same(array(), $values['actions']);

