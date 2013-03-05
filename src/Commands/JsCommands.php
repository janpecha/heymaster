<?php
	/** JS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Commands\Js\IJsShrink,
		Heymaster\Cli\IRunner,
		Heymaster\Logger\ILogger,
		Heymaster\Config\Configurator;
	
	class JsCommands extends CommandSet
	{
		const MASK = '*.js';
		
		/** @var  Heymaster\Commands\Js\IJsShrink */
		private $jsShrink;
		
		/** @var  Heymaster\Cli\IRunner */
		private $runner;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		
		
		public function __construct(IJsShrink $jsShrink, IRunner $runner, ILogger $logger)
		{
			$this->jsShrink = $jsShrink;
			$this->runner = $runner;
			$this->logger = $logger;
		}
		
		
		
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('Js::compress', array($this, 'commandCompress'));
			$configurator->addCommand('Js::compile', array($this, 'commandCompile'));
			$configurator->addCommand('Js::hint', array($this, 'commandHint'));
			
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
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $this->jsShrink($content));
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
			$recursive = (bool) $command->getParameter('recursive', TRUE);
			$filename = (string) $command->getParameter('file', NULL, 'Js::compile: Neni urceno jmeno souboru, do ktereho se ma kompilovat.');
			$creator = $command->findFiles($maskParam)
				->recursive($recursive);
			
			$delimiter = '';
			$filename = $config->root . '/' . $filename;
			file_put_contents($filename, '');
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($filename, $delimiter . jsShrink($content), \FILE_APPEND);
				$delimeter = "\n;";
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandHint(Command $command, Config $config, $mask)
		{
			$maskParam = $command->getParameter('mask', self::MASK);
			// TODO: config file
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			$this->logger->prefix('JS::hint');
			$errors = FALSE;
			
			foreach($creator->find() as $file)
			{
				$this->logger->log((string) $file);
				$output = array();
				$retCode = $this->runner->run(array(
					'jshint',
					$file,
				), $output);
				
				if($retCode)
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
				throw new InvalidException('Any invalid files');
			}
		}
	}

