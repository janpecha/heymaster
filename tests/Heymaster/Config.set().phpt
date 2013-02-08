<?php
/** @version	2013-02-08-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Config.php';


$config = new Heymaster\Config;

// root (string value)
Assert::null($config->root);

$config->set('root', '/');
Assert::same($config->root, '/');

$config->set('root', FALSE);
Assert::same($config->root, '');


// output (bool value)
Assert::null($config->output);

$config->set('output', FALSE);
Assert::false($config->output);

$config->set('output', 'Hello!!!');
Assert::true($config->output);


// message (string value)
Assert::false($config->message);

$config->set('message', 'Hello!!!');
Assert::same($config->message, 'Hello!!!');


// Unknow option
Assert::throws(function() use ($config) {
	$config->set('unknow-option', 'Hello!!!');
}, 'Heymaster\\ConfigUnknowException');

