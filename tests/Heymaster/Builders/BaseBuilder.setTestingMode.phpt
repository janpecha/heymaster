<?php
/** @version	2013-02-06-1 */
use Tester\Assert;

require __DIR__ . '/../bootstrap.php';
require __DIR__ . '/../../../src/Builders/IBuilder.php';
require __DIR__ . '/../../../src/Builders/BaseBuilder.php';

class MyBuilder extends Heymaster\Builders\BaseBuilder
{
	public function getTestingMode()
	{
		return $this->testingMode;
	}
	
	public function getWorkingRoot()
	{
		return $this->root;
	}
	
	function setParameters(array $parameters){}
	function preprocess(){}
	function postprocess(){}
	function finish(){}
}

$builder = new MyBuilder;
Assert::false($builder->getTestingMode());
$builder->setTestingMode();
Assert::true($builder->getTestingMode());
$builder->setTestingMode(FALSE);
Assert::false($builder->getTestingMode());

