<?php
	/** Heymaster PHP Shrink
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Php;
	
	class PhpShrink implements IPhpShrink
	{
		/** @var  PhpShrink */
		private $phpShrink;
		
		
		
		public function __construct(\PhpShrink $shrink = NULL)
		{
			if($shrink === NULL)
			{
				$shrink = new \PhpShrink;
			}
			
			$this->phpShrink = $shrink;
			$this->phpShrink->useNamespaces = TRUE;
		}
		
		
		
		/**
		 * @param	string
		 */
		public function addFile($file)
		{
			return $this->phpShrink->addFile($file);
		}
		
		
		
		/**
		 * @return	string
		 */
		public function getOutput()
		{
			return $this->phpShrink->getOutput();
		}
	}

