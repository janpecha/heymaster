<?php
	/** Heymaster Builder Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-06-1
	 */
	
	namespace Heymaster\Builders;
	
	use Heymaster\Git\IGit,
		Heymaster\Logger\ILogger,
		Heymaster\Builders\BuilderException,
		Heymaster\Files\SimpleManipulator;
	
	class GitBuilder extends BaseBuilder
	{
		const MSG_PREFIX = 'git',
			TEMP_BRANCH_PREFIX = 'heymaster-build-branch-',
			AUTO_BUILD_PREFIX = 'build-',
			DEFAULT_BRANCH = 'master';
		
		/** @var  string  master branch */
		private $branch;
		
		/** @var  string */
		private $oldBranch;
		
		/** @var  string */
		private $tempBranch;
		
		/** @var  string  datetime string in format 'YmdHis' */
		private $date;
		
		/** @var  Heymaster\Git\IGit */
		private $git;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  Heymaster\Files\SimpleManipulator */
		private $manipulator;
		
		
		
		public function __construct(IGit $git, ILogger $logger, SimpleManipulator $manipulator)
		{
			$this->git = $git;
			$this->logger = $logger;
			$this->manipulator = $manipulator;
		}
		
		
		
		public function setParameters(array $parameters)
		{
			$this->parameters = $parameters;
			
			if(!isset($this->parameters['branch']))
			{
				$this->parameters['branch'] = self::DEFAULT_BRANCH;
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	string
		 */
		public function getWorkingRoot()
		{
			return $this->root;
		}
		
		
		
		/**
		 * @param	string|bool  string => name|TRUE => auto tag|FALSE => no tag
		 * @param	Heymaster\Configs\FileConfig
		 * @param	void
		 */
		public function startup($tag)
		{
			parent::startup($tag);
			
			// Get actual datetime
			$this->date = date('YmdHis');
			
			// Check master branch name
			$this->setBranch($this->parameters['branch']); // TODO: asi nee
			$this->logger->prefix(self::MSG_PREFIX)
				->success('Konfigurace v poradku.')
				->end();
			
			// Get current (development) branch
			$this->setOldBranch();
			
			// Generate temp branch name
			$this->generateTempBranchName();
		}
		
		
		
		/**
		 * @return	void
		 */
		public function preprocess()
		{
			// Create temp branch
			$this->logger->prefix(self::MSG_PREFIX)
				->log("Vytvarim docasnou vetev '{$this->tempBranch}' a provadim checkout...");
				
			$this->git->branchCreate($this->tempBranch, TRUE);
			
			$this->logger->success('...ok')
				->end();
		}
		
		
		
		/**
		 * @return	void
		 */
		public function postprocess()
		{
			// Record changes
			$this->git->add('.');
			
			if($this->git->isChanges())
			{
				$this->git->commit("[{$this->date}] Record changes.", array(
					'-a'
				));
			}
			
			// Print test-mode message
			$continue = $this->testModeMessage();
			
			// Transfer changes to master branch
			if($continue)
			{
				$this->transferChanges();
			}
		}
		
		
		
		/**
		 * @return	void
		 */
		public function finish()
		{
			// Create tag
			$this->createTag();
			
			// Checkout on old (dev) branch
			if(!$this->testingMode)
			{
				$this->checkoutOnOld();
			}
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		private function setBranch($name)
		{
			$this->branch = $name;
			
			if(!is_string($this->branch) || $this->branch === '')
			{
				throw new BuilderException(self::MSG_PREFIX . ' Jmeno hlavni vetve neni validni.');
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	$this
		 */
		private function setOldBranch()
		{
			try
			{
				$this->oldBranch = $this->git->branchName();
			}
			catch(GitException $e)
			{
				// neni fatalni - ve vysledku proste jenom neprepneme na puvodni vetev
				$this->oldBranch = FALSE;
			}
			
			if($this->oldBranch === $this->branch)
			{
				throw new BuilderException(self::MSG_PREFIX . " Nelze pokracovat, protoze jste na vetvi, do ktere chcete prenest zmeny.");
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	void
		 */
		private function generateTempBranchName()
		{
			$this->tempBranch = self::TEMP_BRANCH_PREFIX . $this->date;
		}
		
		
		
		/**
		 * @return	string
		 */
		private function generateTagName()
		{
			return self::AUTO_BUILD_PREFIX . $this->date;
		}
		
		
		
		/**
		 * @return	bool  continue?
		 */
		private function testModeMessage()
		{
			if($this->testingMode)
			{
				$this->logger->prefix(self::MSG_PREFIX)->info('Testovaci rezim - HOTOVO.'
					. "\n\t- zustavam na vetvi '{$this->tempBranch}'"
					. "\n\t- neprenasim zmeny do hlavni vetve"
					. "\n\t- nelze spustit sekci 'after'"
					. "\n\t- pro smazani teto vetve se presunte na jinou vetev a spuste prikaz:"
					. "\n\t  \$ git branch -D '{$this->tempBranch}'")
				->end();
				
				return FALSE;
			}
			
			return TRUE;
		}
		
		
		
		private function createTag()
		{
			$tag = $this->tag;
			
			if($tag === TRUE)
			{
				$tag = $this->generateTagName();
			}
			
			$this->logger->prefix(self::MSG_PREFIX);
			
			if(is_string($tag))
			{
				$this->logger->log("Oznacuji sestaveni pomoci tagu '$tag'...");
				$this->git->tag($tag);
				$this->logger->success('...tag vytvoren');
			}
			else
			{
				$this->logger->warn('Nebyl urcen zadny tag pro oznaceni aktualniho sestaveni v hlavni vetvi.');
			}
			
			$this->logger->end();
		}
		
		
		
		private function checkoutOnOld()
		{
			if(is_string($this->oldBranch))
			{
				$this->git->checkout($this->oldBranch);
				return;
			}
			
			$this->logger->prefix(self::MSG_PREFIX);
			$this->logger->warn('Nepodarilo se prepnout na puvodni vetev, provedte to prosim rucne.');
			$this->logger->warn("Aktualni vetev je: {$this->branch}");
			$this->logger->end();
		}
		
		
		
		/**
		 * @return	void
		 */
		private function transferChanges()
		{
			$this->logger->prefix(self::MSG_PREFIX);
			$this->logger->log("Prenasim zmeny do hlavni vetve '{$this->branch}'...");
			
			// Get files list in temp
			$this->logger->log('...ziskavam seznam souboru');
			$fileList = $this->manipulator->getFileList($this->root);
			
			// Checkout on master
			$this->logger->log('...prepinam se na hlavni vetev');
			$this->git->checkout($this->branch);
			
			// Remove old files in master
			$this->removeOldFiles($fileList);
			
			// Merge from temp to master
			$this->logger->log('...provadim merge do hlavni vetve');
			
			$this->git->merge($this->tempBranch, array(
#				'-s' => 'recursive',
				'-X' => 'theirs',
			));
			
			// Remove temp branch
			$this->logger->log("...odstranuji docasnou vetev '{$this->tempBranch}'");
			$this->git->branchRemove($this->tempBranch);
			
			$this->logger->end();
		}
		
		
		
		private function removeOldFiles($fileList)
		{
			$this->logger->log('...odstranuji prebytecne soubory v hlavni vetvi');
			
			// ... get files list in master
			$fileListMaster = $this->manipulator->getFileList($this->root);
			
			// ... calculate files list to remove
			$toRemove = array_diff_key($fileListMaster, $fileList);
			
			// ... remove files
			$git = $this->git;
			
			$this->manipulator->removeFiles($toRemove, TRUE, function($file) use ($git) {
				$git->remove($file);
			});
			
			// ... commit
			if(count($toRemove))
			{
				$this->git->commit("[{$this->date}] Removed unnecessary files and directories.");
			}
		}
	}
	
