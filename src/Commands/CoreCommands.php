<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-10-3
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
			$heymaster->addCommand('merge', array($me, 'commandMerge'));
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
			
			if($cmd === FALSE || !is_string($cmd))
			{
				throw new InvalidException('Neni urceno, ktery prikaz se ma spustit, nebo prikaz neni retezec!');
			}
			
			// Ziskani jmena spousteneho souboru pro pripadne vyhozeni vyjimky
			$commandName = $cmd;
			
			#if(is_array($cmd))
			#{
			#	$commandName = reset($cmd);
			#}
			
			if($cmd[0] === '/' || substr($cmd, 0, 2) === './' || substr($cmd, 0, 3) === '../')
			{
				$cmd = $command->config->root . '/' . $cmd;
			}
			
			// Spusteni prikazu
			$success = $this->heymaster->runner->run($cmd, (bool)$command->config->output);
			
			if($success !== 0 && $throw)
			{
				throw new \UnexpectedValueException("Prikaz '{$commandName}' selhal.");
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandMerge(Command $command, $mask)
		{
			if(!isset($command->params['mask']) && !isset($command->params['masks']))
			{
				throw new InvalidException('Neni nastavena maska pro vstupni soubory.');
			}
			
			if(!isset($command->params['file']) || !is_string($command->params['file']))
			{
				throw new InvalidException('Neni urcen soubor, do ktereho se maji spojit pozadavane soubory.');
			}
			
			$masks = isset($command->params['mask']) ? $command->params['mask'] : $command->params['masks'];
			$filename = $command->params['file'];
			$recursive = isset($command->params['recursive']) ? (bool)$command->params['recursive'] : TRUE;
			
			#foreach($this->heymaster->findFiles($masks)
			#		->mask($mask)
			#	->from($command->config->root) as $file)
			foreach($this->findFilesForMerge($masks, $mask, $command->config->root, $recursive) as $file)
			{
				file_put_contents($command->config->root . "/$filename", file_get_contents($file) . "\n", \FILE_APPEND);
			}
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
		public function commandSymlinks(Command $command, $mask) // ??OK
		{
			if(!isset($command->params['mask']) && !isset($command->params['masks']))
			{
				throw new InvalidException('Neni nastavena maska.');
			}
			
			$masks = isset($command->params['mask']) ? $command->params['mask'] : $command->params['masks'];
			
			/*foreach($this->heymaster->find($masks)
				->in($command->config->root)
					->mask($masks)->mask($mask) as $file)
			*/
			foreach($this->findSymlinks($command->config->root, $masks, $mask) as $file)
			{
				$realpath = $file->getRealPath();
				
				$this->unlink($file);
				$this->copy($realpath, $file);
			}
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
		
		
		
		/**
		 * @param	string
		 * @param	string|string[]
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findSymlinks($dir, $masks, $actionMasks)
		{
			$isLink = function ($file) {
				return $file->isLink();
			};
			
			$finder = $this->heymaster->find($masks)
				->filter($isLink);
			
			$finder->in($dir)
				->filter($isLink);
				
			return $finder;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	string
		 * @param	bool
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findFilesForMerge($masks, $actionMasks, $root, $recursive = TRUE)
		{
			$finder = $this->heymaster->findFiles($masks)
				->mask($actionMasks);
			
			if($recursive)
			{
				$finder->from($root);
			}
			else
			{
				$finder->in($root);
			}
			
			return $finder;
		}
		
		
		
		/**
		 * @param	SplFileInfo
		 * @return	void
		 */
		protected function unlink(\SplFileInfo $file)
		{
			
		}
		
		
		
		/**
		 * @param	string
		 * @param	SplFileInfo	TODO: ??|| string
		 * @return	void
		 */
		protected function copy($realpath, \SplFileInfo $file)
		{
			
		}
	}

