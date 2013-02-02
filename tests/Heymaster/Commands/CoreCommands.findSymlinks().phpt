<?php
/** @version	2013-02-02-1 */
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
	public function findSymlinks($masks, $actionMasks, $dir, $findDirs = FALSE)
	{
		return parent::findSymlinks($masks, $actionMasks, $dir, $findDirs);
	}
}

$heymaster = new Heymaster;

$core = new CoreCommands($heymaster);

Assert::same(array(
	'files/subdir/link-to-file.txt',
), export($core->findSymlinks('*', '*', 'files/subdir', FALSE)));

Assert::same(array(
	'files/subdir/link-to-images-dir',
), export($core->findSymlinks('*', '*', 'files/subdir', TRUE)));


Assert::same(array(), export($core->findSymlinks('*', '*', 'files')));


Assert::same(array(
	'files/subdir/link-to-file.txt',
), export($core->findSymlinks('*.txt', '*', 'files/subdir', FALSE)));


Assert::same(array(
	'files/subdir/link-to-file.txt',
), export($core->findSymlinks('*', '*.txt', 'files/subdir', FALSE)));


Assert::same(array(
	'files/subdir/link-to-file.txt',
), export($core->findSymlinks('*.txt', '*.txt', 'files/subdir', FALSE)));


Assert::same(array(), export($core->findSymlinks('*.txt', '*.php', 'files/subdir', FALSE)));

Assert::same(array(), export($core->findSymlinks('*.php', '*.txt', 'files/subdir', FALSE)));



