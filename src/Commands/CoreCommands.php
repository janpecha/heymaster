<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-07-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command;
	
	class CoreCommands extends CommandSet
	{
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('call', array($me, 'commandRun'));
			$heymaster->addCommand('run', array($me, 'commandRun'));
#			$heymaster->addCommand('merge', array($me, 'commandMerge'));
#			$heymaster->addCommand('touch', array($me, 'commandTouch'));
#			$heymaster->addCommand('symlinks', array($me, 'commandSymlinks'));
#			$heymaster->addCommand('remove', array($me, 'commandRemove'));
#			$heymaster->addCommand('removeContent', array($me, 'commandRemoveContent'));
			
			return $me;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	array
		 * @return	void
		 */
		public function commandRun(Command $command, $mask)
		{
			$success = FALSE;
			$throw = isset($params['fatal']) ? $params['fatal'] : TRUE;
			
			if(is_string($params['params']))
			{
				$success = $this->run($params['params'], NULL, $params['output']);
			}
			elseif(is_array($params['params']))
			{
				$index = NULL;
				
				if(isset($params['params']['cmd']))
				{
					$command = $params['params']['cmd'];
				}
				elseif(isset($params['params']['command']))
				{
					$command = $params['params']['command'];
				}
				elseif(isset($params['params'][0]))
				{
					$command = $params['params'][0];
				}
				else
				{
					throw new \Exception('Chybejici parametr pro ' . $params['name'] . '().');
				}
				
				unset($params['params'][$index]);
				
				$success = $this->run($command, $params['params'], $params['output']);
			}
			else
			{
				throw new \Exception("Spatne parametry pro prikaz '{$params['name']}'");
			}
			
			if(!$success && $throw)
			{
				throw new \Exception("Prikaz '{$params['name']}' selhal.");
			}
		}
		
		
		
		public function commandMerge(array $params)
		{
		
		}
		
		
		
		public function commandTouch(array $params)
		{
			if(isset($params['params']))
			{
				if(!is_array($params['params']))
				{
					$params['params'] = array($params['params']);
				}
				
				foreach($params['params'] as $filename)
				{
					file_put_contents(($filename[0] === '/') ? $filename : $params['root'] . '/' . $filename, '');
				}
			}
			else
			{
				throw new \Exception("Spatne parametry pro prikaz '{$params['name']}'");
			}
		}
		
		
		
		public function commandSymlinks(array $params)
		{
			
		}
		
		
		
		public function commandRemove(array $params)
		{
			
		}
		
		
		
		public function commandRemoveContent(array $params)
		{
			
		}
		
		
		
		/**
		 * @param	string
		 * @param	string[]|NULL
		 * @param	bool
		 * @return	void
		 */
		protected function run($command, $params = NULL, $printOutput = FALSE)
		{
			$params = self::formatRunParams($params);
			
			if($printOutput)
			{
				passthru($command . ' ' . $params);
			}
			else
			{
				exec($command . ' ' . $params);
			}
		}
		
		
		
		/**
		 * @param	array|string
		 * @return	string
		 */
		protected static function formatRunParams($params)
		{
			if(is_array($params))
			{
				$ret = array();
				
				foreach($params as $name => $value)
				{
					if(is_string($name))
					{
						$cmd = '-' . $name . ' ';
						
						if(is_array($value))
						{
							$cmd .= implode(' ', $value);
						}
						else
						{
							$cmd .= $value;
						}
						
						$ret[] = $cmd;
					}
					else
					{
						$ret[] = $value;
					}
				}
				
				return implode(' ', $ret);
			}
			
			return (string)$params;
		}
	}

