<?php
	/** Heymaster Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-2
	 */
	
	namespace Heymaster;
	
	class Config extends \Nette\Object
	{
		/** @var  string */
		public $root;
		
		/** @var  string|FALSE */
		public $message;
		
		/** @var  bool */
		public $output = TRUE;
		
		
		public function set($key, $value)
		{
			switch($key)
			{
				case 'root':
				case 'message':
					$this->$key = (string)$value;
					return;
				
				case 'output':
					$this->output = (bool)$value;
					return;
			}
			
			throw new \Exception("Neznama konfiguracni volba '$key'");
		}
	}

