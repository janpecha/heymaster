<?php
	/** Heymaster File Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-02-1
	 */
	
	namespace Heymaster\Configs;
	
	use Heymaster\Config;
	
	class FileConfig extends Config
	{
		/** @var  bool */
		public $inherit = FALSE;
		
		
		
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
			
			return $ret;
		}
	}
	
