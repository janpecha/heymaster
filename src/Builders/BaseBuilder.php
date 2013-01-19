<?php
	/** Heymaster Abstract Base Builder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-1
	 */
	
	namespace Heymaster\Builders;
	
	abstract class BaseBuilder extends \Nette\Object implements IBuilder
	{
		/** @var  bool */
		protected $testingMode = FALSE;
		
		
		
		/**
		 * @param	bool
		 * @return	$this
		 */
		public function setTestingMode($active)
		{
			$this->testingMode = (bool)$active;
			return $this;
		}
	}
	
