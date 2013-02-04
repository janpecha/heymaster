<?php
/** @version	2013-02-04-1 */
use Tester\Assert,
	Heymaster\Files\SimpleManipulator;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Files/SimpleManipulator.php';

$fixtures = __DIR__ . '/../fixtures';
$imageDir = $fixtures . '/files/images';

$manipulator = new SimpleManipulator($fixtures /*root dir*/);
$images = array(
	$fixtures . '/files/images/gif.php' => 0,
	$fixtures . '/files/images/icon.gif' => 0,
);

$result = array();
$dummyUnlink = function($file) use (&$result) {
	$result[$file] = 0;
};

$manipulator->removeFiles($images, TRUE, $dummyUnlink);
Assert::same($images, $result);


// no use key
$result = array();
$images = array_flip($images);
$manipulator->removeFiles($images, FALSE, $dummyUnlink);
$manipulator->removeFiles($images, $result);


$filesDir = $fixtures . '/files/subdir';
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
$result = array();
$manipulator->removeFiles($files, TRUE, $dummyUnlink);
Assert::same($files, $result);


