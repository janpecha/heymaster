<?php
	/** Heymaster Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-01-1
	 */
	
	namespace Heymaster;
	
	class Config extends \Nette\Object
	{
		/** @var  string */
		public $root;
		
		/** @var  string|FALSE */
		public $message = FALSE;
		
		/** @var  bool */
		public $output;
		
		
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
		
		
		
		/**
		 * @param	string|Heymaster\Config
		 * @param	string|NULL
		 * @return	void
		 */
		public function inherit($config, $property = NULL)
		{
			if(is_string($property))
			{
				$value = $config;
				
				if($config instanceof static)
				{
					$value = $config->$property;
				}
				
				if($property === 'root')	// TODO: co kdyz, chci zdedit celou hodnotu, ne ji jen expandovat?
				{
					$value = self::expandRoot($this->root, $value);
				}
				
				$this->set($property, $value);
			}
			elseif($config instanceof static)
			{
				$this->root = self::expandRoot($this->root, $config->root); // TODO: viz vyse
				$this->output = $config->output;
			}
		}
		
		
		
		/** Rozsiri relativni cestu v prvnim parametru o hodnotu druheho parametru
		 *  Cesta musi existovat.
		 * 
		 * @param	string
		 * @param	string
		 * @return	string
		 * @throws	Heymaster\NotFoundException
		 */
		protected static function expandRoot($toExpand, $expandedBy)
		{
			$value = NULL;
			
			if($toExpand[0] === '/') // absolute path
			{
				$value = $toExpand;
			}
			else
			{
				$value = $expandedBy . '/' . $toExpand;
			}
			
			$ret = realpath($value);
			
			if($ret === FALSE)
			{
				throw new NotFoundException("Root not found: $toExpand");
			}
			
			return $ret;
		}
	}
	
	
	
	class ConfigUnknowException extends \Exception
	{
	}

