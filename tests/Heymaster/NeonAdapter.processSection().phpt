<?php
/** @version	2012-11-27-1 */

use Tester\Assert,
	Tester\Dumper,
	Heymaster\Section,
	Heymaster\Config;

require __DIR__ . '/bootstrap.php';

require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../src/Command.php';
require __DIR__ . '/../../src/Action.php';
require __DIR__ . '/../../src/Section.php';

require __DIR__ . '/../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../src/Adapters/NeonAdapter.php';

class Adapter extends Heymaster\Adapters\NeonAdapter
{
	public function processSection(array $array, Section $section)
	{
		return parent::processSection($array, $section);
	}
}

$adapter = new Adapter;
$section = new Section;
$section->config = new Config;

try
{
	$adapter->processSection(array(
		'any name of action' => FALSE,
	), $section);
}
catch(\Exception $e)
{
	if('Neznama konfiguracni volba' === substr($e->getMessage(), 0, 26))
	{
		throw new Tester\AssertException('Nezachycena vyjimka z metody \'NeonAdapter::processSection()\' po nastaveni neexistujici konfiguracni volby.');
	}
}


