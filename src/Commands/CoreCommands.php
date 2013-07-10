<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Cli\IRunner,
		Heymaster\Files\FileManipulator,
		Heymaster\Config\Configurator;
	
	class CoreCommands extends CommandSet
	{
		/** @var  Heymaster\Cli\IRunner */
		private $runner;
		
		/** @var  Heymaster\Files\FileManipulator */
		private $fileManipulator;
		
		
		
		public function __construct(IRunner $runner, FileManipulator $manipulator)
		{
			$this->runner = $runner;
			$this->fileManipulator = $manipulator;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('call', array($this, 'commandRun'));
			$configurator->addCommand('run', array($this, 'commandRun'));
			$configurator->addCommand('merge', array($this, 'commandMerge'));
			$configurator->addCommand('touch', array($this, 'commandTouch'));
			$configurator->addCommand('symlinks', array($this, 'commandSymlinks'));
			$configurator->addCommand('remove', array($this, 'commandRemove'));
			$configurator->addCommand('removeContent', array($this, 'commandRemoveContent'));
			$configurator->addCommand('replace', array($this, 'commandReplace'));
			
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	UnexpectedValueException
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandRun(Command $command, Config $config, $mask) // OK?
		{
			static $keys = array('cmd', 'command', 0);
			
			$success = FALSE;
			$throw = $command->getParameter('fatal', TRUE);
			
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
				throw new InvalidException('Neni urceno, ktery prikaz se ma spustit, nebo prikaz neni zapsan jako retezec!');
			}
			
			// Ziskani jmena spousteneho souboru pro pripadne vyhozeni vyjimky
			$commandName = $cmd;
			
			#if(is_array($cmd))
			#{
			#	$commandName = reset($cmd);
			#}
			
			if(substr($cmd, 0, 2) === './' || substr($cmd, 0, 3) === '../')
			{
				// TODO: mozna problem s '../' - pokud budeme chytit spustit prikaz mimo nejvyssi workingRoot, chtelo by to upravit chovani
				// TODO: take by mozna stalo za to nepridavat $root  k prikazu, ktery zacina jen na '/'
				$cmd = $config->root . '/' . $cmd;
			}
			
			// Spusteni prikazu
			$output = (bool)$config->output;
			$success = $this->runner->run($cmd, $output);
			
			if($success !== 0 && $throw)
			{
				throw new \UnexpectedValueException("Prikaz '{$commandName}' selhal.");
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandMerge(Command $command, Config $config, $mask) // ??OK
		{
			$masks = $command->getParameter(array('mask', 'masks'), NULL, 'Neni nastavena maska pro vstupni soubory.');
			$file = (string) $command->getParameter('file', NULL, 'Neni urcen soubor, do ktereho se maji pozadavane soubory spojit.');
			$recursive = (bool) $command->getParameter('recursive', TRUE);
			
			$filename = $config->root . "/$file";
			file_put_contents($filename, '');
			
			foreach((array) $masks as $oneMask)
			{
				$creator = $command->findFiles($oneMask)
					->excludeFile($file)
					->recursive($recursive);
				
				foreach($creator->find() as $file)
				{
					file_put_contents($filename, file_get_contents($file) . "\n", \FILE_APPEND);
				}
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandTouch(Command $command, Config $config, $mask) // ?OK
		{
			$masks = $command->getParameter(array('mask', 'masks'), NULL, 'Neni nastavena maska.');
			$force = $command->getParameter('force', FALSE); // Hodnota TRUE smaze obsah souboru
			$flags = $force ? 0 : \FILE_APPEND;
			
			foreach((array) $masks as $fileMask)
			{
				$filename = basename($fileMask);
				$directory = trim(substr($fileMask, 0, -strlen($filename)), '/');
				
				if($directory === '')
				{
					//$directory = '*'; // TODO: je to dobre???
					$touchFileName = $config->root . "/$filename";
					file_put_contents($touchFileName, '', $flags);
					continue;
				}
				
				foreach($command->findDirectories($directory)->findDirectories() as $dir)
				{
					$touchFileName = "$dir/$filename";
					file_put_contents($touchFileName, '', $flags);
				}
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @return	void
		 */
		public function commandSymlinks(Command $command, Config $config, $mask) // ?OK
		{
			$masks = $command->getParameter(array('mask', 'masks'), NULL, 'Neni nastavena maska.');
			
			// Find files
#			$this->processSymlinks($masks, $mask, $command->config->root, FALSE);
			$this->processSymlinks($command->findFiles($masks)
				->filter(array($this->fileManipulator, 'isLink'))
				->find()
			);
			
			// Find dirs
#			$this->processSymlinks($masks, $mask, $command->config->root, TRUE);
			$this->processSymlinks($command->findDirectories($masks)
				->filter(array($this->fileManipulator, 'isLink'))
				->findDirectories()
			);
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @return	void
		 */
		public function commandRemove(Command $command, Config $config, $mask) // ?OK
		{
			$this->processRemove($command, FALSE);
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @return	void
		 */
		public function commandRemoveContent(Command $command, Config $config, $mask) // ?OK
		{
			$this->processRemove($command, TRUE);
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandReplace(Command $command, Config $config, $mask) // ?OK
		{
			$files = (array) $command->getParameter('files', NULL, 'Nejsou urceny soubory k nahrazeni.');
			
			foreach($files as $key => $value)
			{
				if(!is_string($key) || !is_string($value))
				{
					throw new InvalidException('Spatna hodnota - ' . $key . ': ' . $value);
				}
				
				$what = $config->root . '/' . $key;
				$to = $value;
				
				if($to[0] !== '/') // NOT absolute path
				{
					$to = $config->root . '/' . $to;
				}
				
				$mode = $this->fileManipulator->getmod($what);
				$this->fileManipulator->unlink($what);
				$this->fileManipulator->copy($to, $what);
				$this->fileManipulator->chmod($what, $mode);
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Utils\Finder
		 * @return	void
		 */
		protected function processSymlinks($finder) // OK??
		{
			foreach($finder as $file)
			{
				$from = $file->getRealPath();
				$to = (string)$file;
				
				$this->fileManipulator->unlink($file);
				$this->fileManipulator->copy($from, $to, NULL, TRUE);
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	bool
		 * @return	void
		 */
		protected function processRemove(Command $command, $onlyContent = FALSE) // ?Ok
		{
			$masks = $command->getParameter(array('mask', 'masks'), NULL, 'Neni nastavena maska.');
			$ignore = $command->getParameter('ignore', FALSE); // array || string || FALSE (masks)
			
			// Remove files
			$creator = $command->findFiles($masks)->childFirst();
			
			if($ignore !== FALSE)
			{
				$creator->excludeFile($ignore);
			}
			
			foreach($creator->find() as $item)
			{
				$this->fileManipulator->unlink($item, $onlyContent);
			}
			
			// Remove dirs
			$creator = $command->findDirectories($masks)->childFirst();
			
			if($ignore !== FALSE)
			{
				$creator->excludeDir($ignore);
			}
			
			foreach($creator->findDirectories() as $item)
			{
				$this->fileManipulator->unlink($item, $onlyContent);
			}
		}
	}

