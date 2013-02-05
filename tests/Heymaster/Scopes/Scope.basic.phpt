<?php
/** @version	2013-02-05-1 */
use Tester\Assert,
	Heymaster\Utils\Finder,
	Heymaster\Scopes\FinderCreator,
	Heymaster\Scopes\Scope,
	Heymaster\Logger\DefaultLogger,
	Heymaster\Cli\Cli;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../libs/Php-Cli/Cli.php';
require __DIR__ . '/../../../src/Cli/Cli.php';
require __DIR__ . '/../../../src/Section.php';
require __DIR__ . '/../../../src/Logger/ILogger.php';
require __DIR__ . '/../../../src/Logger/DefaultLogger.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Scopes/FinderCreator.php';
require __DIR__ . '/../../../src/Scopes/Scope.php';

$logger = new DefaultLogger;
$root = realpath(__DIR__);
$scope = new Scope($root, $logger);

// default settings
Assert::same($root, $scope->getRoot());
Assert::false($scope->isTestingMode());
Assert::false($scope->getInherit());

// set parent
$parent = new Scope(realpath(__DIR__ . '/../'), $logger);
$scope->setParent($parent);

// set next parent
Assert::throws(function() use ($scope, $parent) {
	$scope->setParent($parent);
}, 'Heymaster\\InvalidException');

// set next parent - not fatal
$scope->setParent($parent, FALSE);

// set testing mode
$scope->setTestingMode();
Assert::true($scope->isTestingMode());
$scope->setTestingMode(FALSE);
Assert::false($scope->isTestingMode());

// set inherit
$scope->setInherit();
Assert::true($scope->getInherit());
$scope->setInherit(FALSE);
Assert::false($scope->getInherit());

// set section
class Section extends Heymaster\Section
{
	public function process()
	{
		echo "section\n";
		return;
	}
}

$before = new Section;
$after = new Section;

$scope->setBefore($before);
Assert::throws(function() use ($scope, $before) {
	$scope->setBefore($before);
}, 'Heymaster\\InvalidException');

$scope->setAfter($after);
Assert::throws(function() use ($scope, $after) {
	$scope->setAfter($after);
}, 'Heymaster\\InvalidException');

// process
function coloredString($str, $color)
{
	return "\033[" . $color . "m" . $str . "\033[0m\r\n";
}

ob_start();
$scope->process();

$output = ob_get_contents();
Assert::same(coloredString("[scope] $root", Cli::COLOR_INFO)
	. "section\nsection\n"
	. coloredString("[scope] Done. $root", Cli::COLOR_SUCCESS)
, $output);
ob_end_clean();



// Bad root
Assert::throws(function() use ($logger) {
	$scope = new Scope(__DIR__ . '/any-unexists-directory', $logger);
}, 'Heymaster\\InvalidException');


// Bad RemoveRoot
class MyScope extends Scope
{
	public function removeRoot($value)
	{
		return parent::removeRoot($value);
	}
}

$myscope = new MyScope(__DIR__, $logger);
Assert::false($myscope->removeRoot('/home/user/project2/my/dir'));

