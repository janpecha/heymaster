<?php
	/** Heymaster Commands CSS Minifier Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Css;
	
	interface ICssMinifier
	{
		/**
		 * @param	string
		 * @return	string
		 */
		function minify($s);
	}

