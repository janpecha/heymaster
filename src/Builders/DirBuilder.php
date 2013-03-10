<?php
	/** Heymaster Dir Builder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Builders;
	
	use Heymaster\Logger\ILogger,
		Heymaster\Files\FileManipulator;
	
	class DirBuilder extends BaseBuilder
	{
		const MSG_PREFIX = 'dir',
			AUTO_BUILD_PREFIX = 'build-';
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  string */
		private $outputDir;
		
		/** @var  Heymaster\Files\FileManipulator */
		private $fileManipulator;
		
		
		
		public function __construct(ILogger $logger, FileManipulator $manipulator)
		{
			$this->logger = $logger;
			$this->fileManipulator = $manipulator;
		}
		
		
		
		/**
		 * @return	string
		 */
		public function getWorkingRoot()
		{
			return $this->outputDir;
		}
		
		
		
		/**
		 * @param	array
		 */
		public function setParameters(array $parameters)
		{
			$this->parameters = $parameters;
			
			if(!isset($this->parameters['directory']))
			{
				throw new BuilderException('Neni nastaven vystupni adresar');
			}
			
			return $this;
		}
		
		
		
		/**
		 * @param	string|bool  string => name|TRUE => auto tag|FALSE => no tag
		 */
		public function startup($tag)
		{
			if(is_bool($tag)) // TRUE or FALSE
			{
				$tag = self::AUTO_BUILD_PREFIX . date('YmdHis');
			}
			
			parent::startup($tag);
			
			// make output dir
			$this->outputDir = (string) $this->parameters['directory'];
			
			if(isset($this->outputDir[0]) && $this->outputDir[0] !== '/') // outputDir !== '' && ...
			{
				$this->outputDir = $this->root . '/' . $this->outputDir;
			}
			
			$this->outputDir .= '/' . $this->tag;
			@mkdir($this->outputDir, 0777, TRUE); // @ - adresar uz muze existovat
			$this->outputDir = realpath($this->outputDir);
			
			if($this->outputDir === FALSE)
			{
				throw new BuilderException('Pri vytvareni vystupniho adresare doslo k chybe. Nelze pokracovat.');
			}
			
			// change current working directory
			chdir($this->outputDir);
			
			// purge output directory
			$this->purge($this->outputDir);
			
			$this->logger->prefix(self::MSG_PREFIX)
				->success('Konfigurace v poradku.')
				->end();
		}
		
		
		
		/**
		 * @return	void
		 */
		public function preprocess()
		{
			$this->logger->prefix(self::MSG_PREFIX)
				->info('Copy files into output directory.')
				->end();
			
			$this->fileManipulator->copy($this->root, $this->outputDir, NULL, TRUE); //TODO:
		}
		
		
		
		/**
		 * Spusteno po zpracovani sekce 'before', ale pred sekci 'after'
		 */
		public function postprocess()
		{
			if($this->testingMode)
			{
				$this->logger->prefix(self::MSG_PREFIX)->info('Testovaci rezim - HOTOVO.'
					. "\n\t- nelze spustit sekci 'after'")
				->end();
			}
		}
		
		
		
		/**
		 * @return	void
		 */
		public function finish()
		{
			// change current working directory
			chdir($this->root);
			
			$this->logger->prefix(self::MSG_PREFIX)
				->success('Done.')
				->end();
		}
		
		
		
		/**
		 * @param	string
		 * @return	void
		 * @author	David Grudl
		 * @edited	by Jan Pecha
		 */
		private function purge($dir)
		{
			@mkdir($dir, 0777, TRUE); // @ - directory may already exist
			
			foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST) as $entry)
			{
				//if (substr($entry->getBasename(), 0, 1) === '.') { // . or .. or .gitignore
				$basename = $entry->getBasename();
		
				if($basename === '.' || $basename === '..')
				{
					// ignore
				}
				elseif($entry->isDir())
				{
					rmdir($entry);
				}
				else
				{
					unlink($entry);
				}
			}
		}
		
		// TODO: jak spustitme deploy z output slozky kdyz nevime kde ji mame - resit?
	}
	
