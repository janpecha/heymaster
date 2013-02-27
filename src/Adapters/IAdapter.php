<?php
	/** Heymaster Adapter Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-27-1
	 */
	
	namespace Heymaster\Adapters;
	
	interface IAdapter
	{
		/**
		 * @param	string
		 * @return	array|FALSE
		 */
		function load($filename);
		
		
		
		/**
		 * @param	string
		 */
		function addWarning($msg);
		
		
		
		/**
		 * @return	string[]
		 */
		public function getWarnings();
	}
	
	
	
	class AdapterException extends \Exception
	{
	}

