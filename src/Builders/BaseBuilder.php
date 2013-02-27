<?php
	/** Heymaster Abstract Base Builder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-06-1
	 */
	
	namespace Heymaster\Builders;
	
	use Heymaster\Configs\FileConfig;
	
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
		public function setTestingMode($active = TRUE)
		{
			$this->testingMode = (bool)$active;
			return $this;
		}
		
		
		
		/**
		 * @param	string|bool  string => name|TRUE => auto tag|FALSE => no tag
		 */
		public function startup($tag)
		{
			$this->tag = $tag;
		}
	}
	
