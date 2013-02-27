<?php
	/** IJsShrink Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Js;
	
	interface IJsShrink
	{
		/**
		 * @param	string
		 * @return	string
		 */
		function shrink($s);
	}

