<?php
	/** Heymaster Action Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-1
	 */
	
	namespace Heymaster;
	
	class Action extends \Nette\Object
	{
		/** @var  string */
		public $name;
		
		/** @var  bool */
		public $runnable = TRUE;
		
		/** @var  string|NULL TODO: ?? */
		public $mask;
		
		/** @var  Config */
		public $config;
		
		/** @var  Command[] */
		public $commands;
	}

