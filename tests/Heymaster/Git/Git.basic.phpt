<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Git\Git;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Git/IGit.php';
require __DIR__ . '/../../../src/Git/Git.php';

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

$tempDir = realpath(TEMP_DIR);
purge($tempDir);
chdir($tempDir);
exec('git init');

$git = new Git;
Assert::throws(function() use ($git) {
	$git->branchName();
}, 'Heymaster\\Git\\GitException');

Assert::false($git->changes); // shortcut for isChanges()

file_put_contents($tempDir . '/index.html', "Lorem ipsum dolor sit\namet.");
$git->add($tempDir . '/index.html');
Assert::true($git->isChanges());
$git->commit('init commit');

Assert::same('master', $git->branchName());
$git->branchCreate('my-branch', TRUE /*checkout*/);
Assert::same('my-branch', $git->branchName());
Assert::false($git->changes);

file_put_contents($tempDir . '/next.html', "Lorem ipsum sit\namet dolor.");
$git->add('.'); // add all
Assert::true($git->isChanges());
$git->commit('second commit');
$git->checkout('master');
Assert::same('master', $git->branchName());
$git->merge('my-branch');
$git->tag('my-tag');
$git->branchRemove('my-branch');
Assert::throws(function() use ($git) {
	$git->checkout('my-branch');
}, 'Heymaster\\Git\\GitException');
Assert::false($git->changes);
$git->remove('index.html');
Assert::true($git->changes);


purge($tempDir);


