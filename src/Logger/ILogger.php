<?php
	/** ILogger interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-1
	 */
	
	namespace Heymaster\Logger;
	
	interface ILogger
	{
		/**
		 * @param	string
		 */
		public function log($str);
		
		
		
		/**
		 * @param	string
		 */
		public function error($str);
		
		
		
		/**
		 * @param	string
		 */
		public function success($str);
		
		
		
		/**
		 * @param	string
		 */
		public function warn($str);
		
		
		
		/**
		 * @param	string
		 */
		public function info($str);
	}

