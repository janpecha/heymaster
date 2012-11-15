<?php
	/** Default Logger
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-15-1
	 */
	
	namespace Heymaster\Logger;
	
	use Heymaster\Cli\Cli;
	
	class DefaultLogger extends \Nette\Object implements ILogger
	{
		public function log($str)
		{
			Cli::log($str);
		}
		
		
		
		public function error($str)
		{
			Cli::error($str);
		}
		
		
		
		public function success($str)
		{
			Cli::success($str);
		}
		
		
		
		public function warn($str)
		{
			Cli::warn($str);
		}
		
		
		
		public function info($str)
		{
			Cli::info($str);
		}
	}

