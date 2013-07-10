<?php
/** @version	2013-02-05-1 */
use Tester\Assert,
	Heymaster\Utils\Finder,
	Heymaster\Scopes\FinderCreator,
	Heymaster\Scopes\Scope,
	Heymaster\Logger\DefaultLogger;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Logger/ILogger.php';
require __DIR__ . '/../../../src/Logger/DefaultLogger.php';
require __DIR__ . '/../../../src/Utils/Finder.php';
require __DIR__ . '/../../../src/Scopes/FinderCreator.php';
require __DIR__ . '/../../../src/Scopes/Scope.php';

$logger = new DefaultLogger;
$root = realpath(__DIR__);
$scope = new Scope($root, $logger);

$params1 = array(
	'git' => array(
		'branch' => 'master',
	),
);

$params2 = array(
	'git' => array(
		'branch' => 'develop',
	),
);

$params3 = array(
	'git' => array(
		'tag' => TRUE,
		'prefix' => array(
			'build' => 'build-%d',
		),
	),
	
	'export' => FALSE,
);

$scope->addParameters($params1);
Assert::same($params1, $scope->getParameters());

$scope->addParameters($params2);
Assert::same($params2, $scope->getParameters());

$scope->addParameters($params3);
Assert::same(array(
	'git' => array(
		'branch' => 'develop',
		'tag' => TRUE,
		'prefix' => array(
			'build' => 'build-%d',
		),
	),
	'export' => FALSE,
), $scope->getParameters());


// Get parameter
$param = $scope->getParameter('git');
Assert::same('develop', $param['branch']);
Assert::false($scope->getParameter('import', FALSE));
Assert::throws(function() use ($scope) {
	$scope->getParameter('un-exists');
}, 'Heymaster\\InvalidException');

