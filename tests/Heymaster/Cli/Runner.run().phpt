<?php
/** @version	2012-12-09-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Cli/IRunner.php';
require __DIR__ . '/../../../src/Cli/Runner.php';


$shell = array('php', '-f' => __DIR__ . '/bin/script.php');
$runner = new Heymaster\Cli\Runner;

// Without output
$output = FALSE;
$code = $runner->run($shell, $output);
Assert::same(2, $code);

// Show output
$output = TRUE;
$code = $runner->run($shell, $output);
Assert::same(2, $code);

// Save output
$output = '';
$code = $runner->run($shell, $output);
Assert::same(2, $code);
Assert::same("Lorem ipsum dolor sit amet,\nlorem ipsum dolor sit amet...", implode("\n",$output));


