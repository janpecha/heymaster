<?php
	/** Heymaster CLI Runner
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-07-1
	 */
	
	namespace Heymaster\Cli;
	
	use Nette\Object;
	
	class Runner extends \Nette\Object implements IRunner
	{
		/**
		 * @param	string|string[]
		 * @param	bool
		 * @return	int  return code
		 */
		public function run($cmd, &$output = FALSE)
		{
			$returnCode = FALSE;
			$cmd = self::buildCommand($cmd);
			
			if($output === FALSE) // output off
			{
				exec($cmd, $unexists, $returnCode);
			}
			elseif($output === TRUE) // show output
			{
				passthru($cmd, $returnCode);
			}
			else // save output
			{
				exec($cmd, $output, $returnCode);
			}
			
			return $returnCode;
		}
		
		
		
		/**
		 * @param	string
		 * @return	string
		 */
		public static function escapeArg($arg)
		{
			return escapeshellarg($arg);
		}
		
		
		
		/**
		 * @param	string[]
		 * @return	string|FALSE
		 */
		public static function buildCommand($args)
		{
			if(!is_array($args))
			{
				$args = array($args);
			}
			
			$cmd = array_shift($args);
			
			if(is_string($cmd))
			{
				$cmd = array($cmd);
				
				foreach($args as $key => $value)
				{
					$c = '';
					
					if(is_string($key))
					{
						$c = self::escapeArg($key) . ' ';
					}
					
					$cmd[] = $c . self::escapeArg($value);
				}
				
				return implode(' ', $cmd);
			}
			
			return FALSE;
		}
	}

