<?php
	/** Heymaster File Config Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-02-1
	 */
	
	namespace Heymaster\Configs;
	
	use Heymaster\Config;
	
	class FileConfig extends Config
	{
		/** @var  string */
		public $branch = 'master';
		
		
		
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
	}
	