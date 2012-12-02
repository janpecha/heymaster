<?php
	/** Heymaster Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-02-2
	 */
	
	namespace Heymaster;
	
	class Config extends \Nette\Object
	{
		/** @var  string */
		public $root;
		
		/** @var  string|FALSE */
		public $message = FALSE;
		
		/** @var  bool */
		public $output = TRUE;
		
		
		/**
		 * @param	string
		 * @param	mixed
		 * @throws	Heymaster\ConfigUnknowException
		 * @return	void
		 */
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
			
			throw new ConfigUnknowException("Neznama konfiguracni volba '$key'");
		}
		
		
		
		/**
		 * @return	array
		 */
		public function toArray()
		{
			return array(
				'root' => $this->root,
				'message' => $this->message,
				'output' => $this->output,
			);
		}
	}
	
	
	
	class ConfigUnknowException extends \Exception
	{
	}

