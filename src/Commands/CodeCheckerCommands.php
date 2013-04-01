<?php
	/** Code Checker Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\Cli\IRunner,
		Heymaster\Logger\ILogger,
		Heymaster\InvalidException,
		Heymaster\Commands\Css\ICssMinifier,
		Heymaster\Config\Configurator;
	
	class CodeCheckerCommands extends CommandSet
	{
		const MASK = '*';
		
		/** @var  Heymaster\Cli\IRunner */
		private $runner;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		
		
		public function __construct(IRunner $runner, ILogger $logger)
		{
			$this->runner = $runner;
			$this->logger = $logger;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('CodeChecker::check', array($this, 'commandCheck'));
			
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCheck(Command $command, Config $config, $mask)
		{
			// promenna $mask je v Command::findFiles() pouzita automaticky
			$maskParam = $command->getParameter(array('mask', 'masks'), self::MASK);
			$convertLines = $command->getParameter('convertLines', FALSE);
			$fixes = $command->getParameter('fixesFiles', FALSE);
			$creator = $command->findFiles($maskParam)
				->recursive();
			$output = TRUE;
			
			$cmd = array(
				'php',
				'-f' => __DIR__ . '/../../tools/Code-Checker/code-checker.php',
				'--',
			);
			
			if($fixes)
			{
				$cmd[] = '-f';
			}
			
			if($convertLines)
			{
				$cmd[] = '-l';
			}
			
			foreach($creator->find() as $file)
			{
				$cmd['-d'] = (string) $file;
				$return = $this->runner->run($cmd, $output);
				
				if($return)
				{
					$this->logger->error('Failed.')
						->end();
				}
			}
			
			$this->logger->success('Done.')
				->end();
		}
	}

