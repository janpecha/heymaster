<?php
	/** Heymaster File Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-02-2
	 */
	
	namespace Heymaster\Configs;
	
	use Heymaster\Config;
	
	class FileConfig extends Config
	{
		/** @var  bool */
		public $inherit = FALSE;
		
		/** @var  string */
		public $builder;
		
		
		
		/**
		 * @param	string
		 * @param	mixed
		 * @return	void
		 */
		public function set($key, $value)
		{
			switch($key)
			{
				case 'inherit':
					$this->$key = (bool)$value;
					return;
				
				case 'builder':
					$this->$key = (string)$value;
					return;
				
				default:
					parent::set($key, $value);
			}
		}
		
		
		
		/**
		 * @return	array
		 */
		public function toArray()
		{
			$ret = parent::toArray();
			$ret['inherit'] = (bool)$this->inherit;
			$ret['builder'] = (string)$this->builder;
			
			return $ret;
		}
	}
	
