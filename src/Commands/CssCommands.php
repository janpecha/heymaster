<?php
	/** CSS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Cli\IRunner,
		Heymaster\Logger\ILogger,
		Heymaster\Commands\Css\ICssMinifier,
		Heymaster\Config\Configurator;
	
	class CssCommands extends CommandSet
	{
		const MASK = '*.css';
		
		/** @var  Heymaster\Commands\Css\ICssMinifier */
		private $minifier;
		
		/** @var  Heymaster\Cli\IRunner */
		private $runner;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		
		
		public function __construct(ICssMinifier $minifier, IRunner $runner, ILogger $logger)
		{
			$this->minifier = $minifier;
			$this->runner = $runner;
			$this->logger = $logger;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('Css::compress', array($this, 'commandCompress'));
			$configurator->addCommand('Css::lint', array($this, 'commandLint'));
			
			return $this;
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
			// promenna $mask je v Command::findFiles() pouzita automaticky
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $this->minifier->minify($content));
			}
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
			// promenna $mask je v Command::findFiles() pouzita automaticky
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			$this->logger->prefix('Css::lint');
			$errors = FALSE;
			
			foreach($creator->find() as $file)
			{
				$this->logger->log((string) $file);
				$output = array();
				
				$code = $this->runner->run(array(
					'csslint',
					'--quiet', // Only output when errors are present.
					(string) $file,
				), $output);
				
				if($code)
				{
					$errors = TRUE;
					
					foreach($output as $line)
					{
						$this->logger->error($line);
					}
				}
			}
			
			$this->logger->end();
			
			if($errors)
			{
				throw new InvalidException('CssLint: Any invalid files');
			}
		}
	}

