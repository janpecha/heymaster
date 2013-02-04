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

/**
 * Purges directory.
 * @param  string
 * @return void
 * @author David Grudl
 * @edited by Jan Pecha
 */
function purge($dir)
{
	@mkdir($dir, 0777, TRUE); // @ - directory may already exist
	foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
		//if (substr($entry->getBasename(), 0, 1) === '.') { // . or .. or .gitignore
		$basename = $entry->getBasename();
		
		if($basename === '.' || $basename === '..') {
			// ignore
		} elseif ($entry->isDir()) {
			rmdir($entry);
		} else {
			unlink($entry);
		}
	}
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

$tempDir = realpath(TEMP_DIR);
purge($tempDir);

$filesDirContent = export(Heymaster\Utils\Finder::find('*')->from($fixtures));
$filesDirContent = removeRoot($filesDirContent, $fixtures);

$manipulator->copy($fixtures, $tempDir);

$tempDirContent = export(Heymaster\Utils\Finder::find('*')->from($tempDir));
$tempDirContent = removeRoot($tempDirContent, $tempDir);

purge($tempDir);
Assert::same($filesDirContent, $tempDirContent);

// Ignore
Assert::null($manipulator->copy($fixtures, $tempDir, 'files'));


// Ignore images
$filesDirContent = export(Heymaster\Utils\Finder::find('*')->from($fixtures)->exclude('images'));
$filesDirContent = removeRoot($filesDirContent, $fixtures);

$manipulator->copy($fixtures, $tempDir, array('images'));

$tempDirContent = export(Heymaster\Utils\Finder::find('*')->from($tempDir));
$tempDirContent = removeRoot($tempDirContent, $tempDir);

purge($tempDir);
Assert::same($filesDirContent, $tempDirContent);


// Bad ignore
$filesDirContent = export(Heymaster\Utils\Finder::find('*')->from($fixtures));
$filesDirContent = removeRoot($filesDirContent, $fixtures);

$manipulator->copy($fixtures, $tempDir, FALSE);

$tempDirContent = export(Heymaster\Utils\Finder::find('*')->from($tempDir));
$tempDirContent = removeRoot($tempDirContent, $tempDir);

purge($tempDir);
Assert::same($filesDirContent, $tempDirContent);

