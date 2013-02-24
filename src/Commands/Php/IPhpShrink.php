<?php
	/** Heymaster PHP Shrink
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Php;
	
	interface IPhpShrink
	{
		/**
		 * @param	string
		 */
		function addFile($file);
		
		
		
		/**
		 * @return	string
		 */
		function getOutput();
	}

