<?php
	/** Php Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Logger\ILogger,
		Heymaster\Cli\IRunner,
		Heymaster\Config\Configurator;
	
	class PhpCommands extends CommandSet
	{
		const MASK = '*.php';
		
		/** @var  Heymaster\Commands\Php\IPhpShrinkFactory */
		private $phpShrinkFactory;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  Heymaster\Cli\IRunner */
		private $runner;
		
		
		
		public function __construct(Php\IPhpShrinkFactory $shrinkFactory, ILogger $logger, IRunner $runner)
		{
			$this->phpShrinkFactory = $shrinkFactory;
			$this->logger = $logger;
			$this->runner = $runner;
		}
		
		
		
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('Php::lint', array($this, 'commandLint'));
			$configurator->addCommand('Php::compress', array($this, 'commandCompress'));
			$configurator->addCommand('Php::compile', array($this, 'commandCompile'));
			
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandLint(Command $command, Config $config, $mask)
		{
			$maskParam = $command->getParameter('mask', self::MASK);
			$output = (bool)$config->output;
			$creator = $command->findFiles($maskParam)
				->recursive();
			$error = FALSE;
			
			foreach($creator->find() as $file)
			{
				$ret = $this->runner->run(array(
					'php -l',
					'-f' => (string)$file,
				), $output);
				
				if($ret !== 0)
				{
					$this->logger->prefix('PHP')
						->error('PhpLint: ' . $file)
						->end();
					$error = TRUE;
				}
			}
			
			if($error)
			{
				throw new InvalidException('Php::lint: Any invalid files.');
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompress(Command $command, Config $config, $mask)
		{
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			foreach($creator->find() as $file)
			{
				$shrink = $this->phpShrinkFactory->createPhpShrink();
				$shrink->addFile($file);
				
				file_put_contents($file, $shrink->getOutput());
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompile(Command $command, Config $config, $mask)
		{
			$maskParam = $command->getParameter('mask', self::MASK);
			$outputFile = (string)$command->getParameter('file', NULL, 'Php::Compile: Nebylo zadano jmeno souboru, do ktereho se ma kompilovat.');
			$creator = $command->findFiles($maskParam)
				->recursive();
			$shrink = $this->phpShrinkFactory->createPhpShrink();
			
			foreach($creator->find() as $file)
			{
				$shrink->addFile($file);
			}
			
			file_put_contents(self::generatePath($outputFile, $config->root), $shrink->getOutput());
		}
		
		
		
		public static function generatePath($filePath, $root)
		{
			if($filePath[0] !== '/') // NOT ABSOLUTE PATH
			{
				return $root . '/' . $filePath;
			}
			
			return $filePath;
		}
	}

