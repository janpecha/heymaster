<?php
	/** Default Logger
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-1
	 */
	
	namespace Heymaster\Logger;
	
	use Heymaster\Cli\Cli;
	
	class DefaultLogger extends \Nette\Object implements ILogger
	{
		/**
		 * @param	string
		 * @return	void
		 */
		public function log($str)
		{
			Cli::log($str);
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		public function error($str)
		{
			Cli::error($str);
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		public function success($str)
		{
			Cli::success($str);
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		public function warn($str)
		{
			Cli::warn($str);
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		public function info($str)
		{
			Cli::info($str);
		}
	}

