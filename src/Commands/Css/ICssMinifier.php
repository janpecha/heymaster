<?php
	/** Heymaster Commands CSS Minifier Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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

