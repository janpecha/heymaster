<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Files\FileManipulator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Files/FileManipulator.php';

$fixtures = __DIR__ . '/../fixtures/files';

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
$manipulator->copy($fixtures, $tempDir);
$manipulator->unlink($tempDir, TRUE);
Assert::same(array(), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$testDir = $tempDir . '/testovaci';
$manipulator->copy($fixtures, $testDir);
$manipulator->unlink($testDir, FALSE);
Assert::same(array(), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$filename = $tempDir . '/any-test-file.txt';
$linkname = $tempDir . '/my-link';
file_put_contents($filename, "Lorem ipsum\ndolor sit amet.");
symlink($filename, $linkname);
$manipulator->unlink($linkname);
Assert::same(array(
	$filename,
), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$dirname = $tempDir . '/my-dir';
$linkname = $tempDir . '/my-link';
mkdir($dirname, 0777);
symlink($dirname, $linkname);
$manipulator->unlink($linkname);
Assert::same(array(
	$dirname,
), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);

// Unlink unexists file
Assert::null($manipulator->unlink('any-unexists-404-file.txt'));


// Remove Content
$file = $tempDir . '/testing-file-to-unlink.txt';
file_put_contents($file, implode("\n", array(
	'Gandalf:',
	'* Greyhame',
	'* Stormcrow',
	'* Gandalf the Grey',
	'* Gandalf the White'
)));

$manipulator->unlink($file, TRUE);
Assert::same('', file_get_contents($file));

purge($tempDir);

