<?php
/** @version	2013-02-24-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
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


// More names
Assert::throws(function () use ($command) {
	$command->getParameter(array('nonexistent-parameter', 'nonexists-param2'), NULL, 'My error message');
}, 'Heymaster\\InvalidException', 'My error message');

Assert::same('Gandalf', $command->getParameter(array('nonexistent-parameter', 'name'), NULL, 'My error message'));

