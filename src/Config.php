<?php
	/** Heymaster Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster;
	use Nette;
	
	class Config extends Nette\Object
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
			elseif(is_string($property))
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
				throw new InvalidException('Prvni parametr musi byt string, nebo Heymaster\\Config.');
			}
			
			return $this;
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
			
			if(isset($toExpand[0]) && $toExpand[0] === '/') // $toExpand !== ('' or NULL) && absolute path
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

