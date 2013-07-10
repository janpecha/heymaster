<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Logger\DefaultLogger,
	Heymaster\Logger\ILogger,
	Heymaster\Cli\Cli;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../libs/Php-Cli/Cli.php';
require __DIR__ . '/../../../src/Cli/Cli.php';
require __DIR__ . '/../../../src/Logger/ILogger.php';
require __DIR__ . '/../../../src/Logger/DefaultLogger.php';

class Logger extends DefaultLogger
{
	public function getPrefix()
	{
		return parent::getPrefix();
	}
}

function coloredString($str, $color)
{
	return "\033[" . $color . "m" . $str . "\033[0m\r\n";
}

// prefix tests
$logger = new Logger;
Assert::same('', $logger->getPrefix());

$logger->prefix('test');
Assert::same('[test] ', $logger->getPrefix());

$logger->prefix('test2');
Assert::same('[test2] ', $logger->getPrefix());

$logger->end();
Assert::same('[test] ', $logger->getPrefix());

$logger->prefix(FALSE);
Assert::same('', $logger->getPrefix());

$logger->end();
Assert::same('[test] ', $logger->getPrefix());

$logger->end();
Assert::same('', $logger->getPrefix());


// output
$logger = new Logger;

ob_start();

$logger->log('Hello!');
$logger->prefix('test')
	->info('Hey hey hello!')
	->error('Erroooor!')
	->end();

$logger->prefix(FALSE)
	->warn('Waaarning!!!')
	->end();

$logger->success('OK');

$output = ob_get_contents();
Assert::same("Hello!\n"
	. coloredString('[test] Hey hey hello!', Cli::COLOR_INFO)
	# STDERR . coloredString('[test] Erroooor!', Cli::COLOR_ERROR)
	# STDERR . coloredString('Waaarning!!!', Cli::COLOR_WARNING)
	. coloredString('OK', Cli::COLOR_SUCCESS)
, $output);
ob_end_clean();

