<?php
/** @version	2013-02-02-1 */

require __DIR__ . '/../../tools/Tester/Tester/bootstrap.php';
require __DIR__ . '/../../libs/Nette/loader.php';
require __DIR__ . '/../../src/exceptions.php';


// create temporary directory
define('TEMP_DIR', __DIR__ . '/../tmp/' . (isset($_SERVER['argv']) ? md5(serialize($_SERVER['argv'])) : getmypid()));
Tester\Helpers::purge(TEMP_DIR);


if (extension_loaded('xdebug'))
{
	Tester\CodeCoverage\Collector::start(__DIR__ . '/../coverage.dat');
}
