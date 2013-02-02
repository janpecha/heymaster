<?php
/** @version	2013-02-02-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/Command.php';

$command = new Heymaster\Command;

$command->params = array(
	'name' => 'Gandalf',
	'alias' => array(
		'Greyhame',
		'Stormcrow',
		'Gandalf the Grey',
		'Gandalf the White'
	),
);

Assert::same($command->params['name'], $command->getParameter('name'));
Assert::same($command->params['alias'], $command->getParameter('alias'));

// Optional parameter
Assert::same('The White Rider', $command->getParameter('nonexistent-parameter', 'The White Rider'));

// Required parameter
Assert::throws(function () use ($command) {
	$command->getParameter('nonexistent-parameter');
}, 'Heymaster\\InvalidException');

Assert::throws(function () use ($command) {
	$command->getParameter('nonexistent-parameter', NULL, 'My error message');
}, 'Heymaster\\InvalidException', 'My error message');

