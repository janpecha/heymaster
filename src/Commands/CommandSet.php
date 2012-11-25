<?php
	/** Command Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Heymaster;
	
	abstract class CommandSet extends Object
	{
		/** @var  Heymaster\Heymaster */
		protected $heymaster;
		
		
		
		public function __construct(Heymaster $heymaster)
		{
			$this->heymaster = $heymaster;
		}
	}

