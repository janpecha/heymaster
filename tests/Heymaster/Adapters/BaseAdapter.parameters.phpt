<?php
/** @version	2013-01-19-1 */

use Tester\Assert,
	Heymaster\Adapters\BaseAdapter;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/AdapterException.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';

class Adapter extends BaseAdapter
{
	public function load($file)
	{
		parent::load($file);
	}
	
	
	public function getParameters()
	{
		return $this->configuration[self::KEY_PARAMETERS];
	}
	
	public function addParameter($name, $value)
	{
		return parent::addParameter($name, $value);
	}
}

$adapter = new Adapter;
Assert::throws(function() use ($adapter) {
	$adapter->addParameter('name', '1');
}, 'Heymaster\\Adapters\\AdapterException');

$adapter->load('file.txt');
Assert::same(array(), $adapter->getParameters());

$adapter->addParameter('git.branch', 'master');
Assert::same(array(
	'git' => array(
		'branch' => 'master',
	),
), $adapter->getParameters());

$adapter->addParameter('git.export.enabled', TRUE);
Assert::same(array(
	'git' => array(
		'branch' => 'master',
		'export' => array(
			'enabled' => TRUE,
		),
	),
), $adapter->getParameters());

$adapter->addParameter('export', array(
	'files' => 'system-files',
));

Assert::same(array(
	'git' => array(
		'branch' => 'master',
		'export' => array(
			'enabled' => TRUE,
		),
	),
	'export' => array(
		'files' => 'system-files',
	),
), $adapter->getParameters());

$adapter->addParameter('git', FALSE);
Assert::same(array(
	'git' => FALSE,
	'export' => array(
		'files' => 'system-files',
	),
), $adapter->getParameters());


