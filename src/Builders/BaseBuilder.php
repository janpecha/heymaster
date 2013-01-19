<?php
	/** Heymaster Abstract Base Builder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-2
	 */
	
	namespace Heymaster\Builders;
	
	abstract class BaseBuilder extends \Nette\Object implements IBuilder
	{
		/** @var  bool */
		protected $testingMode = FALSE;
		
		/** @var  string */
		protected $tag;
		
		
		
		/**
		 * @param	bool
		 * @return	$this
		 */
		public function setTestingMode($active)
		{
			$this->testingMode = (bool)$active;
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 */
		public function startup($tag)
		{
			$this->tag = (string)$tag;
		}
	}
	
