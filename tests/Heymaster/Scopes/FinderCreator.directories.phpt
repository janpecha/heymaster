<?php
/** @version	2013-02-24-1 */
use Tester\Assert,
	Heymaster\Utils\Finder,
	Heymaster\Scopes\FinderCreator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../file-helpers.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Scopes/FinderCreator.php';


$fixtures = realpath(__DIR__ . '/../fixtures/files');

// All dirs - recursive
$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->dirs('*')
	->recursive()
	->findDirectories();

$files = export($finder);
$files = removeRoot($files, $fixtures);
Assert::same(array(
	'/images',
	'/subdir',
	'/subdir/link-to-images-dir',
	'/subdir/subdir2',
	'/subdir/subdir3',
), $files);


// All dirs - one level
$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->dirs('*')
	->recursive(FALSE)
	->findDirectories();

$files = export($finder);
$files = removeRoot($files, $fixtures);
Assert::same(array(
	'/images',
	'/subdir',
), $files);

