<?php
	/** Heymaster PHP Shrink
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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

