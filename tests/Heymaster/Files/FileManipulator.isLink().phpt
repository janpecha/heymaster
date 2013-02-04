<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Files\FileManipulator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Files/FileManipulator.php';

$fixtures = realpath(__DIR__ . '/../fixtures/files');

/** @author	David Grudl */
function export($iterator)
{
	$arr = array();
	foreach ($iterator as $key => $value) $arr[] = strtr($key, '\\', '/');
	sort($arr);
	return $arr;
}

function removeRoot($arr, $root)
{
	$len = strlen($root);
	
	foreach($arr as &$value)
	{
		if(substr($value, 0, $len) === $root)
		{
			$value = substr($value, $len);
		}
	}
	
	return $arr;
}


$manipulator = new FileManipulator;
$export = export($manipulator->findFiles('*')->filter(array($manipulator, 'isLink'))->from($fixtures));

Assert::same(array(
	$fixtures . '/subdir/link-to-file.txt',
#	$fixtures . '/subdir/link-to-images-dir',
), $export);


$export = export($manipulator->findDirectories('*')->filter(array($manipulator, 'isLink'))->from($fixtures));

Assert::same(array(
#	$fixtures . '/subdir/link-to-file.txt',
	$fixtures . '/subdir/link-to-images-dir',
), $export);


