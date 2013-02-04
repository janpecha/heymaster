<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Utils\Finder,
	Heymaster\Scopes\FinderCreator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../file-helpers.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Scopes/FinderCreator.php';

$fixtures = realpath(__DIR__ . '/../fixtures/files');

$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->files('*')
	->excludeFile('.gitignore')
	->excludeFile('*.php')
	->find();

$files = export($finder);
$files = removeRoot($files, $fixtures);
Assert::same(array(
	'/file.txt',
	'/robots.txt',
), $files);



$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->recursive()
	->childFirst()
	->files('*.txt')
	->excludeFile('.gitignore')
	->excludeFile('*.php')
	->excludeDir('subdir/subdir3')
	->find();
	
$files = export($finder);
$files = removeRoot($files, $fixtures);

$finder = Finder::findFiles('*.txt')
	->exclude('.gitignore')
	->exclude('*.php')
	->from($fixtures)
	->exclude('subdir/subdir3')
	->childFirst();
	
$files2 = export($finder);
$files2 = removeRoot($files, $fixtures);

Assert::same($files2, $files);



$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->recursive()
	->files('*.txt')
	->find();
	
$files = export($finder);
$files = removeRoot($files, $fixtures);

$finder = Finder::findFiles('*.txt')
	->from($fixtures);
	
$files2 = export($finder);
$files2 = removeRoot($files, $fixtures);

Assert::same($files2, $files);



$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->recursive()
	->limitDepth(1)
	->files('*.txt')
	->find();
	
$files = export($finder);
$files = removeRoot($files, $fixtures);

$finder = Finder::findFiles('*.txt')
	->from($fixtures)
	->limitDepth(1);
	
$files2 = export($finder);
$files2 = removeRoot($files, $fixtures);

Assert::same($files2, $files);


$isLink = function($file) {
	return $file->isLink();
};

$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->recursive()
	->files('*')
	->filter($isLink) // File Filter
	->find();
	
$files = export($finder);
$files = removeRoot($files, $fixtures);

$finder = Finder::findFiles('*.txt')
	->filter($isLink)
	->from($fixtures);
	
$files2 = export($finder);
$files2 = removeRoot($files, $fixtures);

Assert::same($files2, $files);



$creator = new FinderCreator;
$finder = $creator->directory($fixtures)
	->recursive()
	->files('*')
	->dirs('*imag*')
	->filter($isLink, FALSE) // Directory Filter
	->find();
	
$files = export($finder);
$files = removeRoot($files, $fixtures);

$finder = Finder::findFiles('*.txt')
	->from($fixtures)
	->mask('*imag*')
	->filter($isLink);
	
$files2 = export($finder);
$files2 = removeRoot($files, $fixtures);

Assert::same($files2, $files);


