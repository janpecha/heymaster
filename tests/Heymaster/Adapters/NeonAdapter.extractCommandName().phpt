<?php
/** @version	2012-12-09-1 */

use Tester\Assert,
	Heymaster\Adapters\NeonAdapter as Adapter;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';


Assert::same(Adapter::extractCommandName('commandName'), array(
	'name' => 'commandName',
	'description' => FALSE,
));


Assert::same(Adapter::extractCommandName('commandName any description'), array(
	'name' => 'commandName',
	'description' => 'any description',
));

Assert::same(Adapter::extractCommandName('commandName      any description   '), array(
	'name' => 'commandName',
	'description' => 'any description',
));

Assert::same(Adapter::extractCommandName("commandName \tany description"), array(
	'name' => 'commandName',
	'description' => 'any description',
));


