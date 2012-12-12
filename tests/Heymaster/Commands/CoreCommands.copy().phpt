<?php
/** @version	2012-12-11-1 */
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
	public function copy($from, $to)
	{
		return parent::copy($from, $to);
	}
}

$heymaster = new Heymaster;

$core = new CoreCommands($heymaster);

$tempDir = realpath(TEMP_DIR);
purge($tempDir);

$filesDirContent = export(Heymaster\Utils\Finder::find('*')->from('files'));
$filesDirContent = removeRoot($filesDirContent, 'files');

$core->copy(__DIR__ . '/files', $tempDir);

$tempDirContent = export(Heymaster\Utils\Finder::find('*')->from($tempDir));
$tempDirContent = removeRoot($tempDirContent, $tempDir);

purge($tempDir);
Assert::same($filesDirContent, $tempDirContent);


