<?php
	/** Cli Helper
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-30-1
	 */
	
	namespace Heymaster\Cli;
	
	class Cli extends \Cz\Cli
	{
		const COLOR_ERROR = '1;31';
		
		
		
		/**
		 * @param	string[]	of arguments
		 * @return	array|FALSE
		 */
		public static function parseParams(array $argv)
		{
			$params = array();
			$lastName = NULL;
			
			if(isset($argv[1]))		// count($argv) > 1
			{
				// remove argv[0]
				array_shift($argv);
				
				// parsing
				foreach($argv as $argument)
				{
					if($argument{0} === '-')
					{
						$name = trim($argument, '-');
						
						if($name !== '')
						{
							$lastName = $name;
					
							if(!isset($params[$name]))
							{
								$params[$name] = TRUE;
							}
						}
						else
						{
							$lastName = NULL;
						}
					}
					elseif($lastName === NULL)
					{
						//throw new \Exception("Bad argument '$argument'");
						$params[] = $argument;
					}
					else
					{
						if($params[$lastName] === TRUE)
						{
							$params[$lastName] = $argument;
						}
						else	// string || array
						{
							if(is_string($params[$lastName]))
							{
								$newParams = array(
									$params[$lastName],
									$argument,
								);
							
								$params[$lastName] = $newParams;
							}
							else
							{
								$params[$lastName][] = $argument;
							}
						
							#$lastName = NULL;
						}
					}
				}
				
				return $params;
			}
			
			return FALSE;
		}
	}

