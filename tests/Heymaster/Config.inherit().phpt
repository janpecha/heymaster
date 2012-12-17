<?php
/** @version	2012-12-17-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Config.php';
require __DIR__ . '/../../src/exceptions.php';


$config = new Heymaster\Config;
$config->output = FALSE;
$config2 = new Heymaster\Config;
$config2->root = __DIR__; // must exists!
$config2->output = TRUE;

// root option
$config->root = 'my/root';
Assert::same('my/root', $config->root);

$config->set('root', 'my/second/root');
Assert::same('my/second/root', $config->root);

// inherit output
Assert::false($config->output);
$config->inherit($config2, 'output');
Assert::true($config->output);

// inherit direct value of output
$config->inherit(FALSE, 'output');
Assert::false($config->output);

// inherit root
$config->root = 'Commands/files';
$config->inherit($config2, 'root');
Assert::same(__DIR__ . '/Commands/files', $config->root);

$config->root = 'Commands/files';
$config->output = FALSE;
$config->inherit($config2, 'root');
Assert::same(__DIR__ . '/Commands/files', $config->root);
Assert::false($config->output);

