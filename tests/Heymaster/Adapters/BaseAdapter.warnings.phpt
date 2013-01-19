<?php
/** @version	2013-01-19-1 */

use Tester\Assert,
	Heymaster\Adapters\BaseAdapter;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';

class Adapter extends BaseAdapter
{
	public function load($file)
	{
	}
}

$adapter = new Adapter;
$msg = array(
	'Warning 1',
	'Warning 2',
	'Warning 3',
	'Warning 4',
	'Warning 5',
	'Warning 6',
	'Warning 7',
	'Warning 8',
);

// empty
Assert::same(array(), $adapter->getWarnings());

// one warning
$adapter->addWarning($msg[0]);
Assert::same(array($msg[0]), $adapter->getWarnings());

// more warnings
$count = count($msg);
for($i = 1; $i < $count; $i++)
{
	$adapter->addWarning($msg[$i]);
}

Assert::same($msg, $adapter->getWarnings());

// fluent interface
$msg[] = 'Warning 9';
$msg[] = 'Warning 10';

$adapter->addWarning('Warning 9')
	->addWarning('Warning 10');

Assert::same($msg, $adapter->getWarnings());


