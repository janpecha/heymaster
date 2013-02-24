<?php
/** @version	2013-02-22-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/Config.php';


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
$config->inherit('output', $config2);
Assert::false($config->output); // changed inherit() - for inherit value must be NULL

// inherit direct value of output
$config->inherit('output', FALSE);
Assert::false($config->output);

// inherit root
$config->root = 'Commands/files';
$config->inherit('root', $config2);
Assert::same(__DIR__ . '/Commands/files', $config->root);

$config->root = 'Commands/files';
$config->output = FALSE;
$config->inherit('root', $config2);
Assert::same(__DIR__ . '/Commands/files', $config->root);
Assert::false($config->output);

// inherit all
$config->root = FALSE;
$config->output = NULL; // changed inherit() - for inherit value must be NULL
$config->message = 'Hey hello!';

$config->inherit($config2);

Assert::same(__DIR__, $config->root);
Assert::true($config2->output);
Assert::true($config->output);
Assert::same('Hey hello!', $config->message);

