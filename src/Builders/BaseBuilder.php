<?php
	/** Heymaster Abstract Base Builder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Builders;
	
	use Heymaster\Configs\FileConfig;
	
	abstract class BaseBuilder extends \Nette\Object implements IBuilder
	{
		/** @var  bool */
		protected $testingMode = FALSE;
		
		/** @var  string */
		protected $tag;
		
		/** @var  string|NULL */
		protected $root;
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function setRoot($root)
		{
			$this->root = realpath($root);
			
			if($this->root === FALSE)
			{
				throw new BuilderException('Adresar neexistuje: ' . $root);
			}
			
			return $this;
		}
		
		
		
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
	
