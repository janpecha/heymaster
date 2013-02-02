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

class Heymaster extends \Heymaster\Heymaster
{
	public function __construct()
	{
	}
}

class CoreCommands extends \Heymaster\Commands\CoreCommands
{
	public function copy($from, $to, $ignore = NULL)
	{
		return parent::copy($from, $to, $ignore);
	}
	
	public function unlink($file, $onlyContent = FALSE)
	{
		return parent::unlink($file, $onlyContent);
	}
}

$heymaster = new Heymaster;

$core = new CoreCommands($heymaster);
$tempDir = realpath(TEMP_DIR);

purge($tempDir);
$core->copy(__DIR__ . '/files', $tempDir);
$core->unlink($tempDir, TRUE);
Assert::same(array(), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$testDir = $tempDir . '/testovaci';
$core->copy(__DIR__ . '/files', $testDir);
$core->unlink($testDir, FALSE);
Assert::same(array(), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$filename = $tempDir . '/any-test-file.txt';
$linkname = $tempDir . '/my-link';
file_put_contents($filename, "Lorem ipsum\ndolor sit amet.");
symlink($filename, $linkname);
$core->unlink($linkname);
Assert::same(array(
	$filename,
), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);
$dirname = $tempDir . '/my-dir';
$linkname = $tempDir . '/my-link';
mkdir($dirname, 0777);
symlink($dirname, $linkname);
$core->unlink($linkname);
Assert::same(array(
	$dirname,
), export(Heymaster\Utils\Finder::find('*')->from($tempDir)));


purge($tempDir);

