<?php
	/** JsShrink
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Js;
	use Nette;
	
	class JsShrink extends Nette\Object implements IJsShrink
	{
		/**
		 * @param	string
		 * @return	string
		 */
		public function shrink($s)
		{
			return jsShrink($s);
		}
	}

