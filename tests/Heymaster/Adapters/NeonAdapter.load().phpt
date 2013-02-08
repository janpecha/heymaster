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
	protected function fromFile($filename)
	{
		switch($filename)
		{
			case 'only-string':
				return Neon::decode('Hello!!!');
			
			case 'empty-file':
				return Neon::decode('');
			
			case 'number':
				return Neon::decode('1890');
			
			case 'valid-empty-file':
				return Neon::decode(
"# my test empty file
before:
after:
"
				);
			
			default:
				return FALSE;
		}
	}
	
	
	public static function testCreateConfiguration()
	{
		return self::createConfiguration();
	}
	
}

$adapter = new MyAdapter;

// bad file content
Assert::false($adapter->load('only-string'));
Assert::false($adapter->load('empty-file'));
Assert::false($adapter->load('number'));


// valid empty file
Assert::same($adapter::testCreateConfiguration(), $adapter->load('valid-empty-file'));

