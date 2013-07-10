<?php
	/** NEON Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Nette\Utils\Neon,
		Heymaster\Logger\ILogger,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Config\Configurator;
	
	class NeonCommands extends CommandSet
	{
		const MASK = '*.neon';
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		
		
		/**
		 * @param	Heymaster\Logger\ILogger
		 */
		public function __construct(ILogger $logger)
		{
			$this->logger = $logger;
		}
		
		
		
		/**
		 * @param	Heymaster\Config\Configurator
		 * @return	$this
		 */
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('Neon::lint', array($this, 'commandLint'));
			
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
			// promenna $mask je v Command::findFiles() pouzita automaticky
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			$this->logger->prefix('Neon::lint');
			
			foreach($creator->find() as $file)
			{
				$this->logger->log($file);
				Neon::decode(file_get_contents($file));
			}
			
			$this->logger->success('Done.')
				->end();
		}
	}

