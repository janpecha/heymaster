<?php
	/** IJsShrink Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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

