<?php
/** @version	2012-12-09-1 */

use Tester\Assert,
	Tester\Dumper;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Config.php';
require __DIR__ . '/../../../src/Command.php';
require __DIR__ . '/../../../src/Action.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';

class Adapter extends Heymaster\Adapters\NeonAdapter
{
	public function processAction($name, array $array)
	{
		return parent::processAction($name, $array);
	}
}

$adapter = new Adapter;

Assert::throws(function() use ($adapter) {
	$adapter->processAction('actionName', array(
		'actions' => NULL,
	));
}, '\Heymaster\Adapters\Exception');


Assert::throws(function() use ($adapter) {
	$adapter->processAction('actionName', array());
}, '\Heymaster\Adapters\Exception');


$res = $adapter->processAction('actionName', array(
	'run' => 0,
	'actions' => array(
		'command1' => array(
			'any',
		),
	),
));
Assert::same('actionName', $res->name);
Assert::false($res->runnable);
Assert::same('command1', $res->commands[0]->name);
Assert::same('any', $res->commands[0]->params[0]);


