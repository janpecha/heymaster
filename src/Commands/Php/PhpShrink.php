<?php
	/** Heymaster PHP Shrink
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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
			$this->phpShrink->useNamespaces = FALSE;
		}
		
		
		
		/**
		 * @param	bool
		 * @return	$this
		 */
		public function useNamespaces($useNS = TRUE)
		{
			$this->phpShrink->useNamespaces = (bool) $useNS;
			return $this;
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

