<?php
	/** Heymaster Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-22-1
	 */
	
	namespace Heymaster;
	
	class Config extends \Nette\Object
	{
		/** @var  string */
		public $root;
		
		/** @var  string|FALSE */
		public $message = FALSE;
		
		/** @var  bool|NULL  TRUE = on|FALSE = off|NULL = inherit */
		public $output;
		
		
		/**
		 * @param	string
		 * @param	mixed
		 * @throws	Heymaster\ConfigUnknowException
		 * @return	void
		 */
		public function set($key, $value) // TODO: INHERIT VALUES
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
		 * @param	string|Heymaster\Config|NULL
		 * @return	$this
		 */
		public function inherit($property, $value = NULL)
		{
			/*
				inherit('output', TRUE);
				inherit('output', $config);
				inherit($config);
			*/
			if($property instanceof static)
			{
				if($value !== NULL)
				{
					throw new InvalidException('Zmenil se zpusob pouzivani funkce inherit(). BC-BREAK! Prohodne parametry.');
				}
				
				$this->root = self::expandRoot($this->root, $property->root);
				$this->output = (bool)self::inheritValue($this->output, $property->output);
			}
			elseif(is_string($property) && $value !== NULL)
			{
				if($value instanceof static)
				{
					$value = $value->$property;	
				}
				
				if($property === 'root')
				{
					$value = self::expandRoot($this->root, $value);
				}
				elseif($property === 'output')
				{
					$value = (bool)self::inheritValue($this->output, $value);
				}
				
				$this->$property = $value;
			}
			else
			{
				throw new InvalidException('Prvni parametr musi byt string, nebo Heymaster\\Config, hodnota nesmi byt NULL.');
			}
			
			return $this;
#			if($value !== NULL) // konkretni hodnota
#			{
#				if($value instanceof static)
#				{
#					$value = $value->$property;
#				}
#				
#				if($property === 'root')
#				{
#					$value = self::expandRoot($this->root, $value);
#				}
#				
#				$this->$property = $this->$property === NULL ? $value : $this->$property;
#			}
#			else
#			{
#				$this->root = self::expandRoot($this->root, $property->root);
#				$this->output = $this->output === NULL ? $property->output : $;//TTTTTTTTTTTTTTTTTTTTTTTTTTTTTTT
#			}
#			
#			
#			if(is_string($property)) // inherit one property
#			{
#				if($property === 'output' && $this->output !== NULL)
#				{
#					return;
#				}
#				
#				$value = $config;
#				
#				if($config instanceof static)
#				{
#					$value = $config->$property;
#				}
#				
#				if($property === 'root')	// TODO: co kdyz, chci zdedit celou hodnotu, ne ji jen expandovat?
#				{
#					$value = self::expandRoot($this->root, $value);
#				}
#				
#				$this->set($property, $value);
#			}
#			elseif($config instanceof static) // inherit all config
#			{
#				$this->root = self::expandRoot($this->root, $config->root); // TODO: viz vyse
#				
#				if($this->output === NULL)
#				{
#					$this->output = $config->output;
#				}
#			}
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
		
		
		
		protected static function inheritValue($myValue, $value)
		{
			if($myValue === NULL)
			{
				return $value;
			}
			
			return $myValue;
		}
	}
	
	
	
	class ConfigUnknowException extends \Exception
	{
	}

