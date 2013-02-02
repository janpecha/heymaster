<?php
/** @version	2012-12-11-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
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
	public function findFilesForMerge($masks, $actionMasks, $root, $recursive = TRUE)
	{
		return parent::findFilesForMerge($masks, $actionMasks, $root, $recursive);
	}
}

$heymaster = new Heymaster;

$core = new CoreCommands($heymaster);

Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
	'files/subdir/subdir2/data.txt',
	'files/subdir/subdir3/data.txt',
), export($core->findFilesForMerge('*.txt', '*', 'files/subdir')));


Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
	'files/subdir/subdir2/data.txt',
	'files/subdir/subdir3/data.txt',
), export($core->findFilesForMerge('*', '*.txt', 'files/subdir')));


Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
	'files/subdir/subdir2/data.txt',
	'files/subdir/subdir3/data.txt',
), export($core->findFilesForMerge('*.txt', NULL, 'files/subdir')));



Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
	'files/subdir/subdir2/data.txt',
	'files/subdir/subdir3/data.txt',
), export($core->findFilesForMerge(NULL, '*.txt', 'files/subdir')));


Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
), export($core->findFilesForMerge('*.txt', '*', 'files/subdir', FALSE)));


Assert::same(array(
	'files/subdir/link-to-file.txt',
	'files/subdir/robots.txt',
), export($core->findFilesForMerge('*', '*.txt', 'files/subdir', FALSE)));


Assert::same(array(
	'files/subdir/subdir2/data.txt',
), export($core->findFilesForMerge('*.txt', 'subdir2/*', 'files/subdir')));


Assert::same(array(), export($core->findFilesForMerge('*.txt', 'subdir2/*', 'files/subdir', FALSE)));



