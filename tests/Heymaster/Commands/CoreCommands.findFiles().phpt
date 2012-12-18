<?php
/** @version	2012-12-18-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/exceptions.php';
require __DIR__ . '/../../../src/Heymaster.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Commands/CommandSet.php';
require __DIR__ . '/../../../src/Commands/CoreCommands.php';
require __DIR__ . '/../../../src/Command.php';

/** @author	David Grudl */
function export($iterator)
{
	$arr = array();
	foreach ($iterator as $key => $value) $arr[] = strtr($key, '\\', '/');
	sort($arr);
	return $arr;
}

class Heymaster extends \Heymaster\Heymaster
{
	public function __construct()
	{
	}
}

class CoreCommands extends \Heymaster\Commands\CoreCommands
{
	public function findFiles($mask, $actionMask, $root, $exclude = FALSE, $childFirst = FALSE)
	{
		return parent::findFiles($mask, $actionMask, $root, $exclude, $childFirst);
	}
}

$heymaster = new Heymaster;
$core = new CoreCommands($heymaster);

Assert::same(array(
	'files/subdir/link-to-file.txt',
#	'files/subdir/robots.txt',
	'files/subdir/subdir2/data.txt',
	'files/subdir/subdir3/data.txt',
), export($core->findFiles('*.txt', '*', 'files/subdir', 'robots.txt')));


Assert::same(array(
	'files/subdir/link-to-file.txt',
#	'files/subdir/robots.txt',
#	'files/subdir/subdir2/data.txt',
#	'files/subdir/subdir3/data.txt',
), export($core->findFiles('*.txt', '*', 'files/subdir', array('robots.txt', 'subdir?/*'))));

/*
$finder = $heymaster->findFiles('*.txt')
	->exclude('*.txt')
	->mask('*')
	->from('files/subdir')
	->exclude('.git');

Assert::same(array(), export($finder));
*/
