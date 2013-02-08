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
	
	
	public function processParameters(array $parameters = NULL)
	{
		$this->configuration = self::createConfiguration();
		return parent::processParameters($parameters);
	}
}

$adapter = new Adapter;

// NULL parameters
$adapter->processParameters(NULL);
Assert::same(array(), $adapter->configuration['parameters']);



