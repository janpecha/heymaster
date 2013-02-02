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
	
	
	
	public static function checkValid(array $config)
	{
		return parent::checkValid($config);
	}
}

#Heymaster::checkValid('hello');
$validConfiguration = array(
	'config' => array(),
	'sections' => array(
		'before' => array(),
		'after' => array(),
	),
);

Assert::true(Heymaster::checkValid($validConfiguration));


Assert::throws(function () {
	Heymaster::checkValid(array());	
}, 'Heymaster\\InvalidException');


Assert::throws(function () {
	Heymaster::checkValid(array(
		'config' => array(),
	));	
}, 'Heymaster\\InvalidException');


Assert::throws(function () {
	Heymaster::checkValid(array(
		'sections' => array(),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'config' => array(),
		'sections' => array(),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'config' => array(),
		'sections' => array(
			'before' => array(),
		),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'config' => array(),
		'sections' => array(
			'after' => array(),
		),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'sections' => array(
			'after' => array(),
			'before' => array(),
		),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'sections' => array(
			'before' => array(),
		),
	));	
}, 'Heymaster\\InvalidException');

Assert::throws(function () {
	Heymaster::checkValid(array(
		'sections' => array(
			'after' => array(),
		),
	));	
}, 'Heymaster\\InvalidException');



