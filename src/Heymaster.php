<?php
	/** Heymaster
	 *
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-27-3
	 */
	
	namespace Heymaster;
	
	use Heymaster\Utils\Finder,
		Heymaster\Logger\ILogger,
		Heymaster\Git\IGit,
		Heymaster\Git\GitException,
		Heymaster\Cli\IRunner;
	
	class Heymaster extends \Nette\Object
	{
		/** @var  Heymaster\Logger\ILogger */
		protected $logger;
		
		/** @var  Heymaster\Cli\IRunner */
		protected $runner;
		
		/** @var  string */
		protected $root;
		
		/** @var  bool */
		private $testingMode;
		
		
		
		/**
		 * @param	Heymaster\Logger\ILogger
		 * @param	Heymaster\Cli\IRunner
		 * @param	string
		 */
		public function __construct(ILogger $logger, IRunner $runner, $root) // ok
		{
			$this->logger = $logger;
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
		
		
		
#		/**
#		 * @param	array
#		 * @param	string|NULL|TRUE  tag name, no tag, auto tag
#		 * @param	bool
#		 * @return	void
#		 */
#		public function build(array $configuration, $gitTag = NULL, $isTest = FALSE) // ??OK
#		{
#			$this->testingMode = (bool)$isTest;
#			
#			// Check configuration
#			$this->logger->log('Kontroluji konfiguraci...');
#			self::checkValid($configuration);
#			
#			$this->configuration = $configuration;
#			$this->configuration['config']->inherit($this->root, 'root');
#			$masterBranch = $this->configuration['config']->branch;
#			
#			if(!is_string($masterBranch) && $masterBranch === '')
#			{
#				throw new InvalidException('Jmeno hlavni vetve neni validni.');
#			}
#			
#			$this->logger->success('...ok');
#			
#			// Get current (development) branch
#			$oldBranch = FALSE;
#			
#			try
#			{
#				$oldBranch = $this->git->branchName();
#			}
#			catch(GitException $e) {}
#			
#			if($oldBranch === $masterBranch)
#			{
#				throw new InvalidException("Nelze pokracovat, protoze jste na vetvi, do ktere chcete prenest zmeny.");
#			}
#			
#			// Init
#			$this->logger->log('Vytvarim nove sestaveni...');
#			$date = date('YmdHis');
#			$tempBranchName = 'heymaster-build-branch-' . $date;
#			
#			// Create temp branch
#			$this->logger->log("Vytvarim docasnou vetev '$tempBranchName' a provadim checkout...");
#			$this->git->branchCreate($tempBranchName, TRUE);
#			$this->logger->success('...ok');
#			
#			// Process configuration
#			$this->logger->log('Zpracovavam konfiguraci...');
#			
#			$this->processSectionBlock(self::KEY_BEFORE);
#			
#			$this->git->add('.');
#			
#			if($this->git->isChanges())
#			{
#				$this->git->commit("[$date] Record changes.", array(
#					'-a'
#				));
#			}
#			
#			if($isTest)
#			{
#				$this->logger->info("Testovaci rezim - HOTOVO."
#					. "\n\t- zustavam na vetvi '$tempBranchName'"
#					. "\n\t- neprenasim zmeny do hlavni vetve"
#					. "\n\t- nelze spustit sekci 'after'"
#					. "\n\t- pro smazani teto vetve se presunte na jinou vetev a spuste prikaz:"
#					. "\n\t  > git branch -D '$tempBranchName'");
#				return;
#			}
#			
#			$this->logger->log("Prenasim zmeny do hlavni vetve '$masterBranch'...");
#			$this->logger->log('...vytvarim seznam souboru');
#			$fileList = $this->getFileList($this->root);
#			
#			$this->logger->log('...prepinam se na hlavni vetev');
#			$this->git->checkout($masterBranch);
#			
#			$this->logger->log('...provadim merge do hlavni vetve');
#			$this->git->merge($tempBranchName, array(
#				'-X' => 'theirs',
#			));
#			
#			$this->logger->log("...odstranuji docasnou vetev '$tempBranchName'");
#			$this->git->branchRemove($tempBranchName);
#			
#			$this->logger->log('...odstranuji prebytecne soubory v hlavni vetvi');
#			$fileListMaster = $this->getFileList($this->root);
#			$toRemove = array_diff_key($fileListMaster, $fileList);
#			
#			$this->removeFiles($toRemove, TRUE);
#			
#			if(count($toRemove))
#			{
#				$this->git->commit("[$date] Removed unnecessary files and directories.");
#			}
#			
#			$this->processSectionBlock(self::KEY_AFTER);
#			
#			// Create tag
#			if($gitTag === TRUE)
#			{
#				$gitTag = "build-$tag";
#			}
#			
#			if(is_string($gitTag))
#			{
#				$this->logger->log("Oznacuji sestaveni pomoci tagu '$gitTag'...");
#				$this->git->tag($gitTag);
#				$this->logger->success('...tag vytvoren');
#			}
#			else
#			{
#				$this->logger->warn('Nebyl urcen zadny tag pro oznaceni aktualniho sestaveni v hlavni vetvi.');
#			}
#			
#			// Checkout on old branch
#			if(is_string($oldBranch))
#			{
#				$this->git->checkout($oldBranch);
#			}
#			else
#			{
#				$this->logger->warn('Nepodarilo se prepnout na puvodni vetev, provedte to prosim rucne.');
#				$this->logger->warn("Aktulni vetev je: $masterBranch");
#			}
#			
#			// Done.
#			$this->logger->success('Hotovo.');
#		}
	}

