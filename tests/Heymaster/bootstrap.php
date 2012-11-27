<?php

require __DIR__ . '/../../tools/Tester/Tester/bootstrap.php';
require __DIR__ . '/../../libs/Nette/loader.php';


if (extension_loaded('xdebug')) {
	Tester\CodeCoverage\Collector::start(__DIR__ . '/coverage.dat');
}
