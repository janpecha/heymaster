<?php
	/** Heymaster ILoader interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-27-1
	 */
	
	namespace Heymaster\Config;
	
	interface ILoader
	{
		/**
		 * @param	string
		 * @return	array
		 */
		function load($file);
	}

