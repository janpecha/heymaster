<?php
	/** Heymaster
	 *
	 * REQUIRE NETTE FINDER (in methods findFiles() & findDirectories()).
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-18-1
	 */
	
	namespace Heymaster;
	
	use Heymaster\Utils\Finder,
		Heymaster\Logger\ILogger,
		Heymaster\Git\IGit,
		Heymaster\Git\GitException,
		Heymaster\Cli\IRunner;
	
	class Heymaster extends \Nette\Object
	{
		const KEY_BEFORE = 'before',
			KEY_AFTER = 'after';
		
		
		/** @var  Heymaster\Logger\ILogger */
		protected $logger;
		
		/** @var  Heymaster\Git\IGit */
		protected $git;
		
		/** @var  Heymaster\Cli\IRunner */
		protected $runner;
		
		/** @var  string */
		protected $root;
		
		/** @var  array  'name' => handler */
		protected $commands = array();
		
		/** @var  array */
		protected $configuration;
		
		/** @var  bool */
		private $testingMode;
		
		
		
		/**
		 * @param	Heymaster\Logger\ILogger
		 * @param	Heymaster\Git\IGit
		 * @param	Heymaster\Cli\IRunner
		 * @param	string
		 */
		public function __construct(ILogger $logger, IGit $git, IRunner $runner, $root) // ok
		{
			$this->logger = $logger;
			$this->git = $git;
			$this->runner = $runner;
			$this->root = (string)$root;
		}
		
		
		
		/**
		 * @return	string|NULL
		 */
		public function getRoot()
		{
			return $this->root;
		}
		
		
		
		/**
		 * @return	string|NULL
		 */
		public function getRunner()
		{
			return $this->runner;
		}
		
		
		
		/**
		 * @return	string|NULL
		 */
		public function getLogger()
		{
			return $this->logger;
		}
		
		
		
		/**
		 * @return	bool|NULL
		 */
		public function isTestingMode()
		{
			return $this->testingMode;
		}
		
		
		
		/**
		 * @param	string
		 * @param	callback  (Heymaster\Command $cmd, string $mask)
		 * @return	$this
		 */
		public function addCommand($name, $callback) // ok
		{
			if(isset($this->commands[$name]))
			{
				throw new DuplicateKeyException("Prikaz '$name' uz je zaregistrovan.");
			}
			
			$this->commands[$name] = $callback;
			
			return $this;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function find($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::find($mask);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function findFiles($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findFiles($mask);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function findDirectories($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findDirectories($mask);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @param	bool|var
		 * @return	int  return code
		 */
		public function runExternal($cmd, &$output = FALSE)
		{
			return $this->runner->run($cmd, $output);
		}
		
		
		
		/**
		 * @param	array
		 * @param	string|NULL|TRUE  tag name, no tag, auto tag
		 * @param	bool
		 * @return	void
		 */
		public function build(array $configuration, $gitTag = NULL, $isTest = FALSE) // ??OK
		{
			$this->testingMode = (bool)$isTest;
			
			// Check configuration
			$this->logger->log('Kontroluji konfiguraci...');
			self::checkValid($configuration);
			
			$this->configuration = $configuration;
			$this->configuration['config']->inherit($this->root, 'root');
			$masterBranch = $this->configuration['config']->branch;
			
			if(!is_string($masterBranch) && $masterBranch === '')
			{
				throw new InvalidException('Jmeno hlavni vetve neni validni.');
			}
			
			$this->logger->success('...ok');
			
			// Get current (development) branch
			$oldBranch = FALSE;
			
			try
			{
				$oldBranch = $this->git->branchName();
			}
			catch(GitException $e) {}
			
			if($oldBranch === $masterBranch)
			{
				throw new InvalidException("Nelze pokracovat, protoze jste na vetvi, do ktere chcete prenest zmeny.");
			}
			
			// Init
			$this->logger->log('Vytvarim nove sestaveni...');
			$date = date('YmdHis');
			$tempBranchName = 'heymaster-build-branch-' . $date;
			
			// Create temp branch
			$this->logger->log("Vytvarim docasnou vetev '$tempBranchName' a provadim checkout...");
			$this->git->branchCreate($tempBranchName, TRUE);
			$this->logger->success('...ok');
			
			// Process configuration
			$this->logger->log('Zpracovavam konfiguraci...');
			
			$this->processSectionBlock(self::KEY_BEFORE);
			
			$this->git->add('.');
			
			if($this->git->isChanges())
			{
				$this->git->commit("[$date] Record changes.", array(
					'-a'
				));
			}
			
			if($isTest)
			{
				$this->logger->info("Testovaci rezim - HOTOVO."
					. "\n\t- zustavam na vetvi '$tempBranchName'"
					. "\n\t- neprenasim zmeny do hlavni vetve"
					. "\n\t- nelze spustit sekci 'after'"
					. "\n\t- pro smazani teto vetve se presunte na jinou vetev a spuste prikaz:"
					. "\n\t  > git branch -D '$tempBranchName'");
				return;
			}
			
			$this->logger->log("Prenasim zmeny do hlavni vetve '$masterBranch'...");
			$this->logger->log('...vytvarim seznam souboru');
			$fileList = $this->getFileList($this->root);
			
			$this->logger->log('...prepinam se na hlavni vetev');
			$this->git->checkout($masterBranch);
			
			$this->logger->log('...provadim merge do hlavni vetve');
			$this->git->merge($tempBranchName, array(
				'-X' => 'theirs',
			));
			
			$this->logger->log("...odstranuji docasnou vetev '$tempBranchName'");
			$this->git->branchRemove($tempBranchName);
			
			$this->logger->log('...odstranuji prebytecne soubory v hlavni vetvi');
			$fileListMaster = $this->getFileList($this->root);
			$toRemove = array_diff_key($fileListMaster, $fileList);
			
			$this->removeFiles($toRemove, TRUE);
			
			if(count($toRemove))
			{
				$this->git->commit("[$date] Removed unnecessary files and directories.");
			}
			
			$this->processSectionBlock(self::KEY_AFTER);
			
			// Create tag
			if($gitTag === TRUE)
			{
				$gitTag = "build-$tag";
			}
			
			if(is_string($gitTag))
			{
				$this->logger->log("Oznacuji sestaveni pomoci tagu '$gitTag'...");
				$this->git->tag($gitTag);
				$this->logger->success('...tag vytvoren');
			}
			else
			{
				$this->logger->warn('Nebyl urcen zadny tag pro oznaceni aktualniho sestaveni v hlavni vetvi.');
			}
			
			// Checkout on old branch
			if(is_string($oldBranch))
			{
				$this->git->checkout($oldBranch);
			}
			else
			{
				$this->logger->warn('Nepodarilo se prepnout na puvodni vetev, provedte to prosim rucne.');
				$this->logger->warn("Aktulni vetev je: $masterBranch");
			}
			
			// Done.
			$this->logger->success('Hotovo.');
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 */
		protected function processSectionBlock($name)	// ??OK
		{
			$this->logger->log("Zpracovavam sekci '$name'");
			
			$section = $this->configuration['sections'][$name];
			$section->config->inherit($this->configuration['config']);
			$this->printMessage($section->config);
			$this->process($section);
			$this->logger->success("Sekce '$name' uspesne zpracovana.");
		}
		
		
		
		/**
		 * @param	Heymaster\Section
		 * @return	void
		 */
		protected function process(Section $section) // ??ok
		{
			if(is_array($section->actions))
			{
				foreach($section->actions as $action)
				{
					$action->config->inherit($section->config);
				
					if($action->runnable)
					{
						$this->printMessage($action->config, 'Akce: ' . $action->name);
					
						foreach($action->commands as $command)
						{
							$command->config->inherit($action->config);
							$this->printMessage($command->config, 'Prikaz: ' . $command->name . ' (' . $command->description . ')');
							$this->processCommand($command, $action->mask);
						}
					}
				}
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @return	void
		 */
		protected function processCommand(Command $command, $mask) // ??ok
		{
			if($command->callback === NULL)
			{
				if(!isset($this->commands[$command->name]))
				{
					throw new NotFoundException("Prikaz '{$command->name}' nenalezen.");
				}
				
				$command->callback = $this->commands[$command->name];
			}
			
			call_user_func($command->callback, $command, $mask);
		}
		
		
		
		/**
		 * @param	string
		 * @author	David Grudl, 2009  (modified by Jan Pecha, 2012)
		 * @return	array  [(string)filepath => (int)0]
		 */
		protected function getFileList($dir)	// ?ok
		{
			$res = array();
			$iterator = dir($dir);
			
			while(FALSE !== ($entry = $iterator->read()))
			{
				$path = "$dir/$entry";
				
				if($entry == '.' || $entry == '..' || $entry == '.git')
				{
					continue;
				}
				elseif(!is_readable($path))
				{
					continue;
				}
				#elseif($this->matchMask($path, $this->ignoreMasks))
				#{
				#	//$this->logger->log("Ignoring $path");
				#	continue;
				#}
				elseif(is_dir($path))
				{
					$res["$dir/$entry/"] = 0;//TRUE;
					$res += $this->getFileList("$dir/$entry");
				}
				elseif(is_file($path))
				{
					$res["$dir/$entry"] = 0;//md5_file($this->preprocess($path));
				}
			}
			
			$iterator->close();
			 
			return $res;
		}
		
		
		
		/**
		 * @param	array
		 * @param	bool
		 * @return	void
		 */
		protected function removeFiles($list, $useKey = TRUE) // ??ok
		{
			if(!$useKey)
			{
				$list = array_flip($list);
			}
			
			foreach($list as $file => $value)
			{
				if(file_exists($file))
				{
					$this->git->remove($file);
				}
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Config|string  message
		 * @param	string|NULL  default message
		 * @return	void
		 */
		protected function printMessage($config, $default = NULL)	// ?,OK , TODO: prvni param jen Config???
		{
			$message = $default;
			
			if(is_string($config))
			{
				$message = $config;
			}
			elseif(($config instanceof Config) && is_string($config->message) && $config->message !== '')
			{
				$message = $config->message;
			}
			
			if(is_string($message))
			{
				$this->logger->log($message);
			}
		}
		
		
		
		/**
		 * @param	array
		 * @return	TRUE
		 * @throws	Heymaster\InvalidException
		 */
		protected static function checkValid(array $configuration) // ok
		{
			if(isset($configuration['config'])
				&& isset($configuration['sections'])
				&& isset($configuration['sections'][self::KEY_BEFORE])
				&& isset($configuration['sections'][self::KEY_AFTER]))
			{
				return TRUE;
			}
			
			throw new InvalidException('Konfigurace je nevalidni.');
		}
	}

