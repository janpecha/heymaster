<?php
/** @version	2013-02-02-1 */
use Tester\Assert,
	Heymaster\Files\SimpleManipulator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Files/SimpleManipulator.php';

$imageDir = __DIR__ . '/files/images';

$manipulator = new SimpleManipulator(__DIR__ /*root dir*/);
$images = array(
	__DIR__ . '/files/images/gif.php' => 0,
	__DIR__ . '/files/images/icon.gif' => 0,
);

Assert::same($images, $manipulator->getFileList($imageDir));

$manipulator = new SimpleManipulator($imageDir);
Assert::same($images, $manipulator->getFileList());

$manipulator = new SimpleManipulator(__DIR__);
Assert::same($images, $manipulator->getFileList('files/images'));


$manipulator = new SimpleManipulator(__DIR__);
$filesDir = __DIR__ . '/files/subdir';
$files = array(
	"$filesDir/subdir3/" => 0,
	"$filesDir/subdir3/data.txt" => 0,
	"$filesDir/.htaccess" => 0,
	"$filesDir/link-to-file.txt" => 0,
	"$filesDir/link-to-images-dir/" => 0,
	"$filesDir/link-to-images-dir/gif.php" => 0,
	"$filesDir/link-to-images-dir/icon.gif" => 0,
	"$filesDir/subdir2/" => 0,
	"$filesDir/subdir2/data.txt" => 0,
	"$filesDir/subdir2/lib.php" => 0,
	"$filesDir/lib.php" => 0,
	"$filesDir/robots.txt" => 0,
	"$filesDir/index.php" => 0,
);
Assert::same($files, $manipulator->getFileList('files/subdir'));


