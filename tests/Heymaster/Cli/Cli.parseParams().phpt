<?php
/** @version	2012-12-09-1 */
use Tester\Assert,
	Heymaster\Cli\Cli;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../libs/Php-Cli/Cli.php';
require __DIR__ . '/../../../src/Cli/Cli.php';

Assert::false(Cli::parseParams(array()));
Assert::false(Cli::parseParams(array('program-name')));


$data = array(
	array(
		'input' => array(
			'program-name',
			'-t',
		),
		'output' => array(
			't' => TRUE,
		),
	),
	
	array(
		'input' => array(
			'program-name',
			'-b',
			'name',
		),
		'output' => array(
			'b' => 'name',
		),
	),
	
	array(
		'input' => array(
			'program-name',
			'-b',
			'name',
			'--tag',
		),
		'output' => array(
			'b' => 'name',
			'tag' => TRUE,
		),
	),
	
	array(
		'input' => array(
			'program-name',
			'-b',
			'--',
			'name',
			'--tag',
		),
		'output' => array(
			'b' => TRUE,
			'name',
			'tag' => TRUE,
		),
	),
	
	array(
		'input' => array(
			'program-name',
			'-b',
			'builder',
			'next-builder',
			'next-next-builder',
			'--',
			'name',
			'--tag',
		),
		'output' => array(
			'b' => array(
				'builder',
				'next-builder',
				'next-next-builder',
			),
			'name',
			'tag' => TRUE,
		),
	),
);

foreach($data as $item)
{
	Assert::same($item['output'], Cli::parseParams($item['input']));
}

