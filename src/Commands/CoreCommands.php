<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-07-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\InvalidException;
	
	class CoreCommands extends CommandSet
	{
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('call', array($me, 'commandRun'));
			$heymaster->addCommand('run', array($me, 'commandRun'));
#			$heymaster->addCommand('merge', array($me, 'commandMerge'));
			$heymaster->addCommand('touch', array($me, 'commandTouch'));
#			$heymaster->addCommand('symlinks', array($me, 'commandSymlinks'));
#			$heymaster->addCommand('remove', array($me, 'commandRemove'));
#			$heymaster->addCommand('removeContent', array($me, 'commandRemoveContent'));
			
			return $me;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	UnexpectedValueException
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandRun(Command $command, $mask)
		{
			$success = FALSE;
			$throw = isset($command->params['fatal']) ? $command->params['fatal'] : TRUE;
			static $keys = array('cmd', 'command', 0);
			
			$cmd = FALSE;
			
			foreach($keys as $key)
			{
				if(isset($command->params[$key]))
				{
					$cmd = $command->params[$key];
					break;
				}
			}
			
			if($cmd === FALSE)
			{
				throw new InvalidException('Neni urceno, ktery prikaz se ma spustit!');
			}
			
			$commandName = $cmd;
			
			if(is_array($cmd))
			{
				$commandName = reset($cmd);
			}
			
			$success = $this->heymaster->runner->run($cmd);
			
			if($success !== 0 && $throw)
			{
				throw new \UnexpectedValueException("Prikaz '{$commandName}' selhal.");
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandMerge(Command $command, $mask)
		{
		
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandTouch(Command $command, $mask)
		{
			if(!isset($command->params['mask']) && !isset($command->params['masks']))
			{
				throw new InvalidException('Neni nastavena maska.');
			}
			
			$masks = isset($command->params['mask']) ? $command->params['mask'] : $command->params['masks'];
			$force = (isset($command->params['force'])) ? (bool)$command->params['force'] : FALSE; // Hodnota TRUE smaze obsah souboru
			
			$flags = $force ? 0 : \FILE_APPEND;
			
			if(!is_array($masks))
			{
				$masks = array($masks);
			}
			
			foreach($masks as $fileMask)
			{
				$filename = basename($fileMask);
				$directory = substr($fileMask, 0, -strlen($filename));
				
				if($directory === '')
				{
					$directory = '*';
				}
				// TODO: create directories
				
				foreach($this->heymaster->findDirectories($directory)
					->from($command->config->root)
					->exclude('.git') as $dir)
				{
					$touchFileName = $dir . "/$filename";
					file_put_contents($touchFileName, '', $flags);
				}
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandSymlinks(Command $command, $mask)
		{
			
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandRemove(Command $command, $mask)
		{
			
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandRemoveContent(Command $command, $mask)
		{
			
		}
	}

