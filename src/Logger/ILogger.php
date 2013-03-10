<?php
	/** ILogger interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Logger;
	
	interface ILogger
	{
		/**
		 * @param	string
		 */
		function log($str);
		
		
		
		/**
		 * @param	string
		 */
		function error($str);
		
		
		
		/**
		 * @param	string
		 */
		function success($str);
		
		
		
		/**
		 * @param	string
		 */
		function warn($str);
		
		
		
		/**
		 * @param	string
		 */
		function info($str);
		
		
		
		/**
		 * @param	string
		 */
		function prefix($prefix);
		
		
		
		function end();
	}

