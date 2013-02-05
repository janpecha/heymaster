<?php
/** @version	2013-02-05-1 */
use Tester\Assert,
	Heymaster\Utils\Finder,
	Heymaster\Scopes\FinderCreator,
	Heymaster\Scopes\Scope,
	Heymaster\Logger\DefaultLogger;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../file-helpers.php';
require __DIR__ . '/../../../src/Logger/ILogger.php';
require __DIR__ . '/../../../src/Logger/DefaultLogger.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Scopes/FinderCreator.php';
require __DIR__ . '/../../../src/Scopes/Scope.php';

$root = realpath(__DIR__ . '/../fixtures/files');
$logger = new DefaultLogger;
$scope = new Scope($root, $logger);

$creator = $scope->findFiles('*.txt');
$files = removeRoot(export($creator->find()), $root);
#foreach ($creator->find() as $key => $file) {
#    echo $key, "\n"; // $key je řetězec s názvem souboru včetně cesty
##    echo $file, "\n"; // $file je objektem SplFileInfo
#}
#echo "--------------\n";
#foreach (Nette\Utils\Finder::findFiles('*.txt')->from($root) as $key => $file) {
#    echo $key, "\n"; // $key je řetězec s názvem souboru včetně cesty
##    echo $file, "\n"; // $file je objektem SplFileInfo
#}
#exit;
Assert::same(array(
	'/file.txt',
	'/robots.txt',
	'/subdir/link-to-file.txt',
	'/subdir/robots.txt',
	'/subdir/subdir2/data.txt',
	'/subdir/subdir3/data.txt',
), $files);

// Child
$childRoot = $root . '/subdir/subdir2';
#$f = export(Finder::findFiles('*.txt')
#	->from($root)
#	->exclude('/subdir/subdir2/')
#);

$child = new Scope($childRoot, $logger);
$scope->addChild($child);
$creator = $scope->findFiles('*.txt');
$files = removeRoot(export($creator->find()), $root);

Assert::same(array(
	'/file.txt',
	'/robots.txt',
	'/subdir/link-to-file.txt',
	'/subdir/robots.txt',
#	'/subdir/subdir2/data.txt',
	'/subdir/subdir3/data.txt',
), $files);

// IgnorePaths
$scope = new Scope($root, $logger);
$creator = $scope->findFiles('*');
$files = removeRoot(export($creator->find()), $root);

foreach($files as $file)
{
	echo $file, "\n";
}

Assert::same(array(
	'/boot.php',
	'/file.txt',
	'/images/gif.php',
	'/images/icon.gif',
	'/index.php',
	'/robots.txt',
	'/subdir/.htaccess',
	'/subdir/index.php',
	'/subdir/lib.php',
	'/subdir/link-to-file.txt',
	'/subdir/link-to-images-dir/gif.php',
	'/subdir/link-to-images-dir/icon.gif',
	'/subdir/robots.txt',
	'/subdir/subdir2/data.txt',
	'/subdir/subdir2/lib.php',
	'/subdir/subdir3/data.txt',
), $files);


