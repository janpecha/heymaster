<?php
	/** Heymaster ILoader interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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

