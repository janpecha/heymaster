<?php
	/** Heymaster
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-1
	 */
	
	namespace Heymaster;
	
	use Heymaster\Utils\Finder,
		Heymaster\Logger\ILogger,
		Heymaster\Git\IGit,
		Heymaster\Git\GitException;
	
	class Heymaster extends \Nette\Object
	{
		const KEY_BEFORE = 'before',
			KEY_AFTER = 'after';
		
		
		/** @var  Heymaster\Logger\ILogger */
		protected $logger;
		
		/** @var  Heymaster\Git\IGit */
		protected $git;
		
#		/** @var  Heymaster\IFileManipulator */
#		protected $manipulator;
		
		/** @var  string */
		protected $root;
		
		/** @var  array  name => handler */
		protected $commands = array();
		
		/** @var  array */
		protected $configuration;
		
		
		
		public function __construct(ILogger $logger, IGit $git, $root) // ok
		{
			$this->logger = $logger;
			$this->git = $git;
#			$this->manipulator = $manipulator;
			$this->root = (string)$root;
		}
		
		
		
		/**
		 * @param	string
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
		
		
		
#		/**
#		 * @param	string
#		 * @return	Nette\Utils\Finder
#		 */
#		public function browseFiles($mask, $dir = NULL)
#		{
#			$finder = Finder::findFiles($mask);
#			
#			if(is_string($dir))
#			{
#				$finder->from($dir);
#			}
#			
#			return $finder;
#		}
		
		
		
		public function findFiles($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findFiles($mask);
		}
		
		
		
		public function findDirectories($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findDirectories($mask);
		}
		
		
		
		public function runExternal($cmd, $showOutput = FALSE, $fatal = TRUE)
		{
			
		}
		
		
		
		/**
		 * @param	array
		 * @param	string|NULL  tag name
		 * @return	void
		 */
		public function build(array $configuration, $gitTag = NULL) // ??OK
		{
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
			
			$oldBranch = FALSE;
			
			try
			{
				$oldBranch = $this->git->branchName();
			}
			catch(GitException $e) {}
			
			$this->logger->log('Vytvarim nove sestaveni...');
			$date = date('YmdHis');
			$tempBranchName = 'heymaster-build-branch-' . $date;
			
			$this->logger->log("Vytvarim docasnou vetev '$tempBranchName' a provadim checkout...");
			$this->git->branchCreate($tempBranchName, TRUE);
			$this->logger->success('...ok');
			
			$this->logger->log('Zpracovavam konfiguraci...');
			
			$this->processSectionBlock(self::KEY_BEFORE);
#			
#			// before commands
#			$this->printMessage($this->config[self::KEY_BEFORE], self::KEY_BEFORE);
#			$this->process($this->config[self::KEY_BEFORE]);
#			
#			// after commands
#			if(isset($this->config[self::KEY_AFTER]))
#			{
#				$this->printMessage($this->config[self::KEY_AFTER], self::KEY_AFTER);
#				$this->process($this->config[self::KEY_AFTER]);
#			}
#			
#			$this->checkout($this->config['branch']);
			
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
						$this->printMessage($action->config, $action->name);
					
						foreach($action->commands as $command)
						{
							$command->config->inherit($action->config);
							$this->printMessage($command->config, $command->name);
							$this->processCommand($command, $action->mask);
						}
					}
				}
			}
		}
		
		
		
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
		
		
		
#		/**
#		 * @param	string
#		 * @return	string
#		 */
#		protected function extractName($commandName)
#		{
#			if(($pos = strpos($commandName, ' ')) !== FALSE)
#			{
#				return substr($commandName, 0, $pos);
#			}
#			
#			return $commandName;
#		}
		
		
		
#		/**
#		 * @param	string
#		 * @param	array
#		 * @param	bool
#		 * @param	string
#		 * @return	void
#		 */
#		protected function runCommand($name, $params, $printOutput, $root)
#		{
#			if(!isset($this->commands[$name]))
#			{
#				throw new \Exception("Prikaz '$name' neexistuje.");
#			}
#			
#			$params = array(
#				'name' => $name,
#				'output' => $printOutput,
#				'params' => $params,
#				'root' => $root,
#			);
#			
#			call_user_func($this->commands[$name], $params);
#		}
		
		
		
#		protected function generateAbsoluteRoot($action)
#		{
#			//;(isset($action['root']) ? $action['root'] : $this->config['root']);
#			$root = NULL;
#			
#			if(isset($action['root']))
#			{
#				$root = $action['root'];
#				
#				if($root[0] !== '/')
#				{
##					return realpath($root);
##				}
##				else
##				{
#					$root = $this->config['root'] . '/' . $root;
##					return realpath($this->config['root'] . '/' . $root);
#				}
#			}
#			else
#			{
#				$root = $this->config['root'];
#			}
#			
#			return realpath($root);
#		}
		
		
		
		/**
		 * @param	string
		 * @author	David Grudl, 2009
		 * @author	Jan Pecha, 2012
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
				$this->git->remove($file);
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
#			if(isset($category['message']) && is_scalar($category['message']))
#			{
#				$this->logger->log($category['message']);
#			}
#			else
#			{
#				$this->logger->log("Running '$categoryName'...");
#			}
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

