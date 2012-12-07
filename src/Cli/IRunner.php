<?php
	/** Heymaster CLI Runner Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-07-1
	 */
	
	namespace Heymaster\Cli;
	
	interface IRunner
	{
		/**
		 * @param	string|string[]
		 * @param	bool
		 * @return	int  return code
		 */
		public function run($cmd, &$output);
		
		
		
		/**
		 * @param	string
		 * @return	string
		 */
		public static function escapeArg($arg);
	}

