<?php
	/** Heymaster Command Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-02-1
	 */
	
	namespace Heymaster;
	
	class Command extends \Nette\Object
	{
		/** @var  string|FALSE */
		public $name;
		
		/** @var  string|FALSE optional */
		public $description = FALSE;
		
		/** @var  callback */
		public $callback;
		
		/** @var  array */
		public $params;
		
		/** @var  Config */
		public $config;
		
		
		
		/**
		 * @param	string  name of parameter
		 * @param	mixed|NULL  default value (optional parameter), NULL => required parameter
		 * @param	string|NULL  error message
		 * @return	mixed
		 */
		public function getParameter($name, $default = NULL, $message = NULL)
		{
			$name = (string)$name;
			if(!isset($this->params[$name]))
			{
				if($default !== NULL)
				{
					return $default;
				}
				
				throw new InvalidException($message !== NULL ? (string)$message : "Parameter $name is required.");
			}
			
			return $this->params[$name];
		}
	}

