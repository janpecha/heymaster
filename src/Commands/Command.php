<?php
	/** Command Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-19-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Heymaster;
	
	abstract class Command extends Object
	{
		protected $heymaster;
		
		
		
		public function __construct(Heymaster $heymaster)
		{
			$this->heymaster = $heymaster;
		}
	}

