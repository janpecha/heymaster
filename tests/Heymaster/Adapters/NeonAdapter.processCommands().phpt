<?php
/** @version	2012-12-09-1 */

use Tester\Assert,
	Tester\Dumper;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Command.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';

class Adapter extends Heymaster\Adapters\NeonAdapter
{
	public function processCommands(array $array, $parentName)
	{
		return parent::processCommands($array, $parentName);
	}
}

$adapter = new Adapter;

$res = $adapter->processCommands(array(
	'command' => array(
		'arg0', 'arg1',
	),
), '');

Assert::same(count($res), 1);
Assert::same('command', $res[0]->name);
Assert::same('arg0', $res[0]->params[0]);
Assert::same('arg1', $res[0]->params[1]);
Assert::false($res[0]->description);


$res = $adapter->processCommands(array(), '');
Assert::same(count($res), 0);


$res = $adapter->processCommands(array(
	'command with a description' => array(
		'arg0' => 'first',
		'arg1' => 'second',
	),
), '');

Assert::same(count($res), 1);
Assert::same('command', $res[0]->name);
Assert::same('first', $res[0]->params['arg0']);
Assert::same('second', $res[0]->params['arg1']);
Assert::same('with a description', $res[0]->description);

