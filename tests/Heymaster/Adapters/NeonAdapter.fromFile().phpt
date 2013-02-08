<?php
/** @version	2013-02-08-1 */

use Tester\Assert,
	Nette\Utils\Neon;

require __DIR__ . '/../bootstrap.php';

require __DIR__ . '/../../../src/Adapters/IAdapter.php';
require __DIR__ . '/../../../src/Adapters/BaseAdapter.php';
require __DIR__ . '/../../../src/Adapters/NeonAdapter.php';

class MyAdapter extends Heymaster\Adapters\NeonAdapter
{
	public function fromFile($filename)
	{
		return parent::fromFile($filename);
	}
	
	
	public static function testCreateConfiguration()
	{
		return self::createConfiguration();
	}
	
}

$adapter = new MyAdapter;

// file not found
Assert::throws(function () use ($adapter) {
	$adapter->load(__DIR__ . '/not-found-file.neon');
}, 'Nette\InvalidArgumentException');

