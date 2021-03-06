<?php
	/** Php Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Cz,
		Jp,
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
		
		/** @var  Cz\PhpDepend */
		private $phpDepend;
		
		/** @var  Jp\Dependency */
		private $dependency;
		
		
		
		public function __construct(Php\IPhpShrinkFactory $shrinkFactory,
			ILogger $logger,
			IRunner $runner,
			Cz\PhpDepend $phpDepend,
			Jp\Dependency $dependency)
		{
			$this->phpShrinkFactory = $shrinkFactory;
			$this->logger = $logger;
			$this->runner = $runner;
			$this->phpDepend = $phpDepend;
			$this->dependency = $dependency;
		}
		
		
		
		/**
		 * @return	$this
		 */
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
			
			$this->logger->prefix('Php::lint')
				->log('Start...');
			
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
			
			$this->logger->success('Done.')
				->end();
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
			$useNamespaces = (bool) $command->getParameter('useNamespaces', FALSE);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			$this->logger->prefix('Php::compress');
			
			foreach($creator->find() as $file)
			{
				$shrink = $this->phpShrinkFactory->createPhpShrink();
				$shrink->useNamespaces($useNamespaces);
				$shrink->addFile($file);
				
				file_put_contents($file, $shrink->getOutput());
				$this->logger->success($file);
			}
			
			$this->logger->end();
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
			// get params
			$maskParam = $command->getParameter('mask', self::MASK);
			$outputFile = (string)$command->getParameter('file', NULL, 'Php::Compile: Nebylo zadano jmeno souboru, do ktereho se ma kompilovat.');
			$useNamespaces = (bool) $command->getParameter('useNamespaces', FALSE);
			$netteRobots = (bool) $command->getParameter('netteRobots', TRUE);
			
			// settings of services
			$creator = $command->findFiles($maskParam)
				->recursive();
			$shrink = $this->phpShrinkFactory->createPhpShrink();
			$shrink->useNamespaces($useNamespaces);
			$this->dependency->reset();
			
			// variables
			$classes = array();
			$files = array();
			
			// compile
			$this->logger->prefix('Php::compile')
				->log('Start...');
			
			// scan
			foreach($creator->find() as $file)
			{
				$this->logger->log("$file");
				$this->phpDepend->parse(file_get_contents($file));
				
				foreach($this->phpDepend->getClasses() as $class)
				{
					$classes[$class] = (string) $file;
				}
				
				$files[(string)$file] = $this->phpDepend->getDependencies();
			}
			
			// build depends
			foreach($files as $file => $depends)
			{
				$depFiles = array();
				
				foreach($depends as $depend => $value)
				{
					if(isset($classes[$depend]))
					{
						$depFiles[] = $classes[$depend];
					}
				}
				
				$this->dependency->add($file, $depFiles);
			}
			
			// compile
			foreach($this->dependency->getResolved() as $file)
			{
				$shrink->addFile($file);
				$this->logger->log("Added file: $file");
			}
			
			$content = $shrink->getOutput();
			
			if($netteRobots)
			{
				$netteRobots = '//netterobots=' . implode(',', array_keys($classes));
				$content = substr_replace($content, "<?php $netteRobots\n", 0, 5);
			}
			
			$content = str_replace("\r\n", "\n", $content);
			$content = trim(preg_replace("#[\t ]+(\r?\n)#", '$1', $content)); // right trim
			
			$path = self::generatePath($outputFile, $config->root);
			
			file_put_contents($path, $content);
			$this->logger->success("Done. $path")
				->end();
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

