<?php
/** @version	2013-02-26-1 */
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

$creator = $scope->findDirectories('sub*');
$files = removeRoot(export($creator->findDirectories()), $root);

Assert::same(array(
	'/subdir',
	'/subdir/subdir2',
	'/subdir/subdir3',
), $files);

// Child
$childRoot = $root . '/subdir/subdir2';

$child = new Scope($childRoot, $logger);
$scope->addChild($child);
$creator = $scope->findDirectories('*');
$files = removeRoot(export($creator->findDirectories()), $root);

Assert::same(array(
	'/images',
	'/subdir',
	'/subdir/link-to-images-dir',
	'/subdir/subdir3',
), $files);

#// IgnorePaths
#$scope = new Scope($root, $logger);
#$creator = $scope->findDirectories('*');
#$files = removeRoot(export($creator->findDirectories()), $root);

#Assert::same(array(
#), $files);


