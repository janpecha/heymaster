<?php
	/** Heymaster File Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-1
	 */
	
	namespace Heymaster\Configs;
	
	use Heymaster\Config;
	
	class FileConfig extends Config
	{
		/** @var  string */
		public $branch = 'master';
		
		
		
		/**
		 * @param	string
		 * @param	mixed
		 * @return	void
		 */
		public function set($key, $value)
		{
			switch($key)
			{
				case 'branch':
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
			$ret['branch'] = $this->branch;
			
			return $ret;
		}
	}
	
