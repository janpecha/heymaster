<?php
/** @version	2013-02-01-1 */
use Tester\Assert;

require __DIR__ . '/bootstrap.php';
require __DIR__ . '/../../src/exceptions.php';
require __DIR__ . '/../../src/Config.php';


class Config extends Heymaster\Config
{
	public static function expandRoot($toExpand, $expandedBy)
	{
		return parent::expandRoot($toExpand, $expandedBy);
	}
}

Assert::same(__DIR__, Config::expandRoot(__DIR__, '/any-directory'));
Assert::same(__DIR__, Config::expandRoot(basename(__DIR__), __DIR__ . '/../'));

Assert::throws(function() {
	Config::expandRoot('unexists-directory', __DIR__);
}, 'Heymaster\NotFoundException');

