<?php
	/** Heymaster DI Container
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	use Nette\DI;
	
	class HeymasterContainer extends DI\Container
	{
		const BUILDER_GIT = 'git',
			BUILDER_ZIP = 'zip';
		
		public $parameters = array(
			'root' => NULL,
			'builder' => self::BUILDER_GIT,
			//'cacheDir' => (string),
		);
		
		
		
		public function createServiceHeymaster()
		{
			return new Heymaster\Heymaster($this->logger);
		}
		
		
		
		public function createServiceAdapter()
		{
			return new Heymaster\Adapters\NeonAdapter;
		}
		
		
		
		public function createServiceRobotLoader()
		{
			$loader = new Nette\Loaders\RobotLoader;
			$loader->autoRebuild = FALSE;
			$loader->setCacheStorage($this->robotLoaderCache);
			
			return $loader;
		}
		
		
		
		public function createServiceCli()
		{
			return new Heymaster\Cli\Cli;
		}
		
		
		
		public function createServiceCoreCommands()
		{
			return new Heymaster\Commands\CoreCommands(
				$this->runner,
				$this->fileManipulator
			);
		}
		
		
		
		public function createServiceCssCommands()
		{
			return new Heymaster\Commands\CssCommands($this->cssMinifier);
		}
		
		
		
		public function createServiceJsCommands()
		{
			return new Heymaster\Commands\JsCommands($this->jsShrink);
		}
		
		
		
		public function createServicePhpCommands()
		{
			return new Heymaster\Commands\PhpCommands(
				$this->phpShrinkFactory,
				$this->logger,
				$this->runner
			);
		}
		
		
		
		public function createServiceConfigurator()
		{
			$configurator = new Heymaster\Config\Configurator(
				$this->configLoader,
				$this->logger
			);
			
			$this->coreCommands->install($configurator);
			$this->cssCommands->install($configurator);
			$this->jsCommands->install($configurator);
			$this->phpCommands->install($configurator);
			
			return $configurator;
		}
		
		
		
		protected function createServiceConfigLoader()
		{
			return new Heymaster\Config\Loader($this->adapter);
		}
		
		
		
		protected function createServiceLogger()
		{
			return new Heymaster\Logger\DefaultLogger;
		}
		
		
		
		protected function createServiceBuilder()
		{
			if($this->parameters['builder'] === self::BUILDER_GIT)
			{
				return $this->gitBuilder;
			}
#			elseif($this->parameters['builder'] === self::BUILDER_ZIP)
#			{
#				return $this->zipBuilder;
#			}
			
			throw new InvalidException('Unknow builder ID: ' . $this->parameters['builder']);
		}
		
		
		
		protected function createServiceRunner()
		{
			return new Heymaster\Cli\Runner;
		}
		
		
		
		protected function createServiceGit()
		{
			return new Heymaster\Git\Git;
		}
		
		
		
		protected function createServiceSimpleManipulator()
		{
			return new Heymaster\Files\SimpleManipulator($this->parameters['root']);
		}
		
		
		
		protected function createServiceFileManipulator()
		{
			return new Heymaster\Files\FileManipulator();
		}
		
		
		
		protected function createServiceGitBuilder()
		{
			return new Heymaster\Builders\GitBuilder(
				$this->git,
				$this->logger,
				$this->simpleManipulator
			);
		}
		
		
		
		protected function createServiceCssMinifier()
		{
			return new Heymaster\Commands\Css\CssMinifier;
		}
		
		
		
		protected function createServiceJsShrink()
		{
			return new Heymaster\Commands\Js\JsShrink;
		}
		
		
		
		protected function createServicePhpShrinkFactory()
		{
			return new Heymaster\Commands\Php\PhpShrinkFactory;
		}
		
		
		
		protected function createServiceRobotLoaderCache()
		{
			return new Nette\Caching\Storages\FileStorage($this->parameters['cacheDir']);
		}
	}

