<?php
	/** Heymaster Command Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-1
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
	}

