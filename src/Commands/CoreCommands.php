<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-12-3
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
			$heymaster->addCommand('symlinks', array($me, 'commandSymlinks'));
			$heymaster->addCommand('remove', array($me, 'commandRemove'));
			$heymaster->addCommand('removeContent', array($me, 'commandRemoveContent'));
			
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
				$directory = trim(substr($fileMask, 0, -strlen($filename)), '/');
				
				if($directory === '')
				{
					//$directory = '*'; // TODO: je to dobre???
					$touchFileName = $command->config->root . "/$filename";
					file_put_contents($touchFileName, '', $flags);
					continue;
				}
				// TODO: create directories
				
				#foreach($this->heymaster->findDirectories($directory)
				#	->from($command->config->root)
				#	->exclude('.git') as $dir)
				foreach($this->findDirectories($directory, $mask, $command->config->root) as $dir)
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
			// Find files
			$this->processSymlinks($masks, $mask, $command->config->root, FALSE);
			
			// Find dirs
			$this->processSymlinks($masks, $mask, $command->config->root, TRUE);
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandRemove(Command $command, $mask)
		{
			$this->processRemove($command, $mask, FALSE);
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		public function commandRemoveContent(Command $command, $mask)
		{
			$this->processRemove($command, $mask, TRUE);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	string  find only files or only dirs
		 * @param	bool
		 * @return	void
		 */
		protected function processSymlinks($mask, $actionMask, $root, $findDirs = FALSE)
		{
			foreach($this->findSymlinks($mask, $actionMask, $root, $findDirs) as $file)
			{
				$from = $file->getRealPath();
				$to = (string)$file;
				
				$this->unlink($file);
				$this->copy($from, $to);
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @param	bool
		 * @return	void
		 */
		protected function processRemove(Command $command, $actionMask, $onlyContent = FALSE)
		{
			if(!isset($command->params['mask']) && !isset($command->params['masks']))
			{
				throw new InvalidException('Neni nastavena maska.');
			}
			
			$masks = isset($command->params['mask']) ? $command->params['mask'] : $command->params['masks'];
			$ignore = isset($command->params['ignore']) ? $command->params['ignore'] : FALSE;
			
			// Remove files
			foreach($this->findFiles($masks, $actionMask, $command->config->root, $ignore, /*??*/TRUE/*child first*/) as $item)
			{
				$this->unlink($item, $onlyContent);
			}
			
			// Remove dirs
			foreach($this->findDirectories($masks, $actionMask, $command->config->root, $ignore, /*??*/TRUE/*child first*/) as $item)
			{
				$this->unlink($item, $onlyContent);
			}
		}
		
		
		
		/**
		 * @param	string
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	bool
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findSymlinks($masks, $actionMasks, $dir, $findDirs = FALSE)
		{
			$finder = NULL;
			
			if($findDirs)
			{
				$finder = $this->heymaster->findDirectories($masks);
			}
			else
			{
				$finder = $this->heymaster->findFiles($masks);	
			}
			
			$finder->mask($actionMasks)
				->filter(array($this, 'isLink'));
			
			$finder->in($dir);
			
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
			
			$finder->exclude('.git');
			
			return $finder;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	string
		 * @param	string|string[]|FALSE
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findFiles($mask, $actionMask, $root, $exclude = FALSE/*, /*??*//*TRUE/*child first*/)
		{
			$finder = $this->heymaster->findFiles($mask)
				->mask($actionMask);
			
			if($exclude !== FALSE)
			{
				$finder->exclude($exclude);
			}
			
			$finder->from($root)
				->exclude('.git');
			
			return $finder;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	string
		 * @param	string|string[]|FALSE
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findDirectories($mask, $actionMask, $root, $exclude = FALSE/*, /*??*//*TRUE/*child first*/)
		{
			$finder = $this->heymaster->findDirectories($mask)
				->mask($actionMask)
				->exclude('.git');
			
			if($exclude !== FALSE)
			{
				$finder->exclude($exclude);
			}
			
			$finder->from($root)
				->exclude('.git');
			
			return $finder;
		}
		
		
		
		/**
		 * @param	string
		 * @param	bool
		 * @return	void
		 */
		protected function unlink($file, $onlyContent = FALSE)
		{
			$file = (string)$file;
			
			if(is_file($file))
			{
				if($onlyContent)
				{
					file_put_contents((string)$file, '');
					return;
				}
			}
			elseif(is_dir($file))
			{
				// browse child
				foreach($this->heymaster->find('*')->in($file)->childFirst() as $item)
				{
					$this->unlink($item, FALSE);
				}
				
				if($onlyContent)
				{
					return;
				}
				
				rmdir($file);
				return;
			}
			
			unlink($file);
		}
		
		
		
		/**
		 * @param	string	from
		 * @param	string	to
		 * @return	void
		 */
		protected function copy($from, $to)
		{
			// TODO: co se symlinky, ted se symlinky nezachovaji, ale zkopiruje se to, na co ukazuji - je to OK??
			if(is_file($from))
			{
				copy($from, $to);
			}
			elseif(is_dir($from))
			{
				/*$r = */@mkdir($to, 0777, TRUE); // adresar uz muze existovat
				
#				if($r === FALSE)
#				{
#					throw new \RuntimeException("Pri vytvareni adresare '$to' doslo k chybe!");
#				}
				
				$this->unlink($to, TRUE); // remove content
				
				foreach($this->heymaster->find('*')->in($from) as $file)
				{
					$this->copy((string)$file, $to . '/' . $file->getBasename());
				}
			}
		}
		
		
		
		/**
		 * @param	SplFileInfo
		 * @return	bool
		 */
		public static function isLink($file)
		{
			return $file->isLink();
		}
	}

