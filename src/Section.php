<?php
	/** Heymaster Action Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-1
	 */
	
	namespace Heymaster;
	
	class Section extends \Nette\Object
	{
		/** @var  string|FALSE */
		public $name;
		
		/** @var  Heymaster\Config */
		public $config;
		
		/** @var  Heymaster\Actions[] */
		public $actions;
	}

