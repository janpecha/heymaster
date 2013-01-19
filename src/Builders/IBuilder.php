<?php
	/** Heymaster Builder Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-1
	 */
	
	namespace Heymaster\Builders;
	
	interface IBuilder
	{
		/**
		 * @param	bool
		 */
		public function setTestingMode($active);
		
		
		
		/**
		 * @param	string
		 */
		public function startup($tag);
		
		
		
		public function finish();
	}
	
	
	
	class Exception extends \Exception
	{
	}
