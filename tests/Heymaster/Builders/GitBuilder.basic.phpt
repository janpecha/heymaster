<?php
/** @version	2013-02-05-1 */
use Tester\Assert,
	Heymaster\Builders\IBuilder,
	Heymaster\Builders\GitBuilder,
	Heymaster\Git\Git,
	Heymaster\Files\SimpleManipulator,
	Heymaster\Logger\DefaultLogger;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../file-helpers.php';
require __DIR__ . '/../../../libs/Php-Cli/Cli.php';
require __DIR__ . '/../../../src/Cli/Cli.php';
require __DIR__ . '/../../../src/Logger/ILogger.php';
require __DIR__ . '/../../../src/Logger/DefaultLogger.php';
require __DIR__ . '/../../../src/Files/SimpleManipulator.php';
require __DIR__ . '/../../../src/Git/IGit.php';
require __DIR__ . '/../../../src/Git/Git.php';
require __DIR__ . '/../../../src/Builders/IBuilder.php';
require __DIR__ . '/../../../src/Builders/BaseBuilder.php';
require __DIR__ . '/../../../src/Builders/GitBuilder.php';

$tempDir = realpath(TEMP_DIR);
purge($tempDir);
$loremIpsum = "Lorem Ipsum dolor\n\tsit amet.";
$logger = new DefaultLogger;
$manipulator = new SimpleManipulator($tempDir);
$git = new Git;

chdir($tempDir);
exec('git init');

$builder = new GitBuilder($git, $logger, $manipulator);
$builder->setRoot($tempDir);
$builder->setParameters(array(
	'branch' => 'master',
));


// init commit
file_put_contents("$tempDir/.gitignore", '');
file_put_contents("$tempDir/my-file.txt", 'Hello!');
$git->add('.gitignore');
$git->commit('init commit');
$git->add('my-file.txt');
$git->commit('second commit');
$git->branchCreate('develop', TRUE);

// step 1 - develop
// ... 1.1)
$git->remove('.gitignore');
$git->remove('my-file.txt');
file_put_contents("$tempDir/file4.txt", $loremIpsum);
$git->add('file4.txt');
$git->commit('added file4.txt');

// ... 1.2)
file_put_contents("$tempDir/file1.html", '');
file_put_contents("$tempDir/file2.txt", $loremIpsum);
$git->add('file1.html');
$git->add('file2.txt');
$git->commit('added file1 & file2');

// ... 1.3)
file_put_contents("$tempDir/file3.html", '');
file_put_contents("$tempDir/file5.html", $loremIpsum);
file_put_contents("$tempDir/file6.html", $loremIpsum);
$git->add('file3.html');
$git->add('file5.html');
$git->add('file6.html');
$git->commit('added file3 & file5 & file6');

// build
$builder->startup(TRUE); // TRUE => auto tag
$builder->preprocess();

// ... 'before' section
$addLoremIpsum = "$loremIpsum\nby Gandalf\n";
$addDelLoremIpsum = explode("\n", $loremIpsum);
$delLoremIpsum = $addDelLoremIpsum[0];
$addDelLoremIpsum = $addDelLoremIpsum[1] . "\nby Gandalf The White\n";

//$git->remove('file3.html');
unlink("$tempDir/file3.html");
file_put_contents("$tempDir/file2.txt", $addDelLoremIpsum);
file_put_contents("$tempDir/file1.html", $addLoremIpsum);
file_put_contents("$tempDir/file4.txt", $delLoremIpsum);
file_put_contents("$tempDir/new1.html", $loremIpsum);
rename("$tempDir/file5.html", "$tempDir/file5.txt");
#$git->add('file2.txt')
#	->add('file1.html')
#	->add('file4.txt')
#	->add('new1.html')
#	->add('file5.html')
#	->add('file5.txt');


// ... finish
$builder->postprocess();

Assert::same(array(
	'/file1.html',
	'/file2.txt',
	'/file4.txt',
	'/file5.txt',
	'/file6.html',
	'/new1.html',
), removeRoot(export($manipulator->getFileList($tempDir)), $tempDir));

$builder->finish();

Assert::same(array(
	'/file1.html',
	'/file2.txt',
	'/file3.html',
	'/file4.txt',
	'/file5.html',
	'/file6.html',
), removeRoot(export($manipulator->getFileList($tempDir)), $tempDir));


// Iteration 2
Assert::same('develop', $git->branchName());

// TODO: remove a file here?

$builder = new GitBuilder($git, $logger, $manipulator);
$builder->setRoot($tempDir);
$builder->setParameters(array(
	'branch' => 'master',
));

$builder->startup('my-second-build'); // TRUE => auto tag
$builder->preprocess();

file_put_contents("$tempDir/file1.html", $loremIpsum . "\n" . $addDelLoremIpsum);
file_put_contents("$tempDir/new2.html", $loremIpsum);

$builder->postprocess();

Assert::same(array(
	'/file1.html',
	'/file2.txt',
	'/file4.txt',
	'/file6.html',
	'/new2.html',
), removeRoot(export($manipulator->getFileList($tempDir)), $tempDir));

$builder->finish();

Assert::same(array(
	'/file1.html',
	'/file2.txt',
	'/file3.html',
	'/file4.txt',
	'/file5.html',
	'/file6.html',
), removeRoot(export($manipulator->getFileList($tempDir)), $tempDir));


// Next test
purge($tempDir);
system('git init');
file_put_contents('.gitignore', '');
$git->add('.');
$git->commit('first commit');
$builder = new GitBuilder($git, $logger, $manipulator);

// unexists directory
Assert::throws(function() use ($builder) {
	$builder->setRoot(__DIR__ . '/any-unexists-directory');
}, 'Heymaster\\Builders\\BuilderException');

$builder->setRoot($tempDir);
$builder->setParameters(array(
	'branch' => 'master',
));

// same branch names
Assert::throws(function () use ($builder) {
	$builder->startup(TRUE); // TRUE => auto tag
}, 'Heymaster\\Builders\\BuilderException');


// bad branch name
$builder->setParameters(array(
	'branch' => 10,
));
Assert::throws(function () use ($builder) {
	$builder->startup(TRUE); // TRUE => auto tag
}, 'Heymaster\\Builders\\BuilderException');

$builder->setParameters(array(
	'branch' => '',
));
Assert::throws(function () use ($builder) {
	$builder->startup(TRUE); // TRUE => auto tag
}, 'Heymaster\\Builders\\BuilderException');



purge($tempDir);

