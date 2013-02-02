<?php
/** @version	2013-02-02-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Heymaster.php';


class Heymaster extends Heymaster\Heymaster
{
	public function __construct()
	{
	}
	
	
	
	public function getCommands()
	{
		return $this->commands;
	}
}


// Duplicate key
$heymaster = new Heymaster;

Assert::throws(function () use ($heymaster) {
	$heymaster->addCommand('commandName', 'callback1');
	$heymaster->addCommand('commandName', 'callback2');
}, 'Heymaster\\DuplicateKeyException');

unset($heymaster);


// Add commands
$heymaster = new Heymaster;
Assert::same(array(), $heymaster->getCommands());
Assert::same(0, count($heymaster->getCommands()));

$heymaster->addCommand('command1', 'callback1');
Assert::same(array(
	'command1' => 'callback1',
), $heymaster->getCommands());
Assert::same(1, count($heymaster->getCommands()));


$heymaster->addCommand('command2', 'callback2');
Assert::same(array(
	'command1' => 'callback1',
	'command2' => 'callback2',
), $heymaster->getCommands());
Assert::same(2, count($heymaster->getCommands()));


