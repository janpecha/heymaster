<?php
	/** Command Set Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Heymaster;
	
	abstract class CommandSet extends Object
	{
		/** @var  Heymaster\Heymaster */
		protected $heymaster;
		
		
		
		/**
		 * @param	Heymaster\Heymaster
		 */
		public function __construct(Heymaster $heymaster)
		{
			$this->heymaster = $heymaster;
		}
	}

