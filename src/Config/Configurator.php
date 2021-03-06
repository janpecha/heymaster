<?php
	/** Heymaster Configurator
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Config;
	use Nette,
		Heymaster\Logger\ILogger,
		Heymaster\Scopes\Scope,
		Heymaster\Section,
		Heymaster\Config,
		Heymaster\Action,
		Heymaster\Command,
		Heymaster\InvalidException,
		Heymaster\DuplicateKeyException,
		Heymaster\NotFoundException,
		Heymaster\Builders\IBuilder,
		Nette\Config\Helpers;
	
	class Configurator extends Nette\Object
	{
		/** @var  array */
		protected $files = array();
		
		/** @var  array  'name' => handler */
		protected $commands = array();
		
		/** @var  Heymaster\Config\ILoader */
		protected $loader;
		
		/** @var  Heymaster\Logger\ILoader */
		protected $logger;
		
		/** @var  array */
		private $parameters;
		
		/** @var  Heymaster\Builders\IBuilder */
		private $builder;
		
		
		
		public function __construct(ILoader $loader, ILogger $logger)
		{
			$this->loader = $loader;
			$this->logger = $logger;
			$this->parameters = self::getDefaultParameters();
		}
		
		
		
		/**
		 * @return	array
		 */
		public function getParameters()
		{
			return $this->parameters;
		}
		
		
		
		/**
		 * @param	array
		 * @return	$this
		 */
		public function addParameters(array $parameters)
		{
			$this->parameters = Helpers::merge($parameters, $this->parameters);
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Builders\IBuilder
		 * @return	$this
		 */
		public function setBuilder(IBuilder $builder)
		{
			if($this->builder)
			{
				throw new InvalidException('Configurator: Builder je jiz nastaven.');
			}
			
			$this->builder = $builder;
			return $this;
		}
		
		
		
		/**
		 * @return	Heymaster\Builders\IBuilder
		 */
		public function getBuilder()
		{
			return $this->builder;
		}
		
		
		
		/**
		 * Adds config file (heymaster.neon)
		 * @param	string
		 * @return	$this
		 */
		public function addConfig($file)
		{
			$this->files[] = (string)$file;
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @param	callback  (Heymaster\Command $cmd, string $mask)
		 * @return	$this
		 */
		public function addCommand($name, $handler)
		{
			if(isset($this->commands[$name]))
			{
				throw new DuplicateKeyException("Prikaz '$name' uz je zaregistrovan.");
			}
			
			$this->commands[$name] = $handler;
			
			return $this;
		}
		
		
		
		/**
		 * @return	Heymaster\Scopes\Scope[]
		 */
		public function buildScopes()
		{
			$scopes = array();
			
			// sort files
			sort($this->files, SORT_STRING);
			
			foreach($this->files as $file)
			{
				$configuration = $this->loader->load($file);
				
				if($configuration === FALSE)
				{
					continue; // TODO: throw error?
				}
				
				$dir = dirname($file);
				$scopes[$dir] = $this->process($dir, $configuration);
			}
			
			// navazani vztahu mezi Scopy
			$relNode = $this->findRelations($scopes);
			$mainScope = $this->makeRelations($relNode);
			
			return $mainScope;
		}
		
		
		
		/**
		 * @param	array
		 * @return	Heymaster\Config\RelNode
		 */
		protected function findRelations(array $scopes)
		{
			$root = $this->createRelNode(''/*FS ROOT*/, NULL);
			
			foreach($scopes as $dir => $scope)
			{
				$root->addChild($dir, $scope);
			}
			
			return $root->getFirstFilled();
		}
		
		
		
		/**
		 * @param	Heymaster\Config\RelNode
		 * @return	Heymaster\Scopes\Scope
		 */
		protected function makeRelations($relNode)
		{
			$mainScope = $relNode->scope;
			
			foreach($relNode->getNearestChildren() as $childNode)
			{
				if($childNode->scope instanceof Scope)
				{
					$mainScope->addChild($childNode->scope);
					$this->makeRelations($childNode);
				}
			}
			
			return $mainScope;
		}
		
		
		
		/**
		 * @param	string
		 * @param	array
		 * @return	Heymaster\Scopes\Scope
		 */
		protected function process($root, array $configuration)
		{
			$scope = $this->createScope($root);
			
			foreach($configuration as $key => $value)
			{
				switch($key)
				{
					case 'parameters':
						$scope->addParameters($value);
						continue;
					
					case 'root':
						$scope->setProcessRoot($value);
						continue;
					
					case 'output':
						$scope->setOutput($value);
						continue;
					
					case 'inherit':
						$scope->setInherit($value);
						continue;
					
					case 'message':
						// nothing - TODO: messages
						continue;
					
					case 'sections':
						$this->processSections($scope, $value);
						continue;
					
					// default: TODO: throw exception???
				}
			}
			
			return $scope;
		}
		
		
		
		/**
		 * @param	Heymaster\Scopes\Scope
		 * @param	array
		 * @return	void
		 */
		protected function processSections(Scope $scope, $arr)
		{
			foreach($arr as $key => $value)
			{
				if($value === NULL)
				{
					continue;
				}
				
				$section = $this->processSection($key, $value);
				
				if($key === 'before')
				{
					$scope->setBefore($section);
				}
				elseif($key === 'after')
				{
					$scope->setAfter($section);
				}
			}
		}
		
		
		
		/**
		 * @param	string
		 * @param	array
		 * @return	Heymaster\Section
		 */
		protected function processSection($name, $arr)
		{
			$section = $this->createSection();
			$section->name = $name;
			$section->config = $this->createConfig();
			
			foreach($arr as $key => $value)
			{
				switch($key)
				{
					case 'root':
					case 'output':
					case 'message':
						$section->config->set($key, $value);
						continue;
					
					case 'actions':
						foreach($value as $actionKey => $actionValue)
						{
							$section->actions[] = $this->processAction($actionKey, $actionValue);
						}
						continue;
				}
			}
			
			return $section;
		}
		
		
		
		/**
		 * @param	string
		 * @param	array
		 * @return	Heymaster\Action
		 */
		protected function processAction($name, $arr)
		{
			$action = $this->createAction();
			$action->name = $name;
			$action->config = $this->createConfig();
			
			foreach($arr as $key => $value)
			{
				switch($key)
				{
					case 'root':
					case 'output':
					case 'message':
						$action->config->set($key, $value);
						continue;
					
					case 'run':
						$action->runnable = (bool) $value;
						continue;
					
					case 'mask':
						$action->mask = $value;
						continue;
					
					case 'commands':
						foreach($value as $command)
						{
							$action->commands[] = $this->processCommand($command);
						}
						continue;
				}
			}
			
			return $action;
		}
		
		
		
		/**
		 * @param	array
		 * @return	Heymaster\Command
		 */
		protected function processCommand($arr)
		{
			$command = $this->createCommand();
			$command->config = $this->createConfig();
			
			foreach($arr as $key => $value)
			{
				switch($key)
				{
					case 'name':
						$command->name = $value;
						continue;
					
					case 'description':
						$command->description = $value;
						continue;
					
					case 'params':
						$command->params = $value;
						continue;
						
					case 'root':
					case 'output':
					case 'message':
						$command->config->set($key, $value);
						continue;
				}
			}
			
			if(isset($this->commands[$command->name]))
			{
				$command->callback = $this->commands[$command->name];
			}
			else
			{
				throw new NotFoundException("Unknow command: '{$command->name}'");
			}
			
			return $command;
		}
		
		
		
		/**
		 * @return	Heymaster\Scopes\Scope
		 */
		protected function createScope($root)
		{
			return new Scope($root, $this->logger);
		}
		
		
		
		/**
		 * @return	Heymaster\Section
		 */
		protected function createSection()
		{
			return new Section;
		}
		
		
		
		/**
		 * @return	Heymaster\Action
		 */
		protected function createAction()
		{
			return new Action;
		}
		
		
		
		/**
		 * @return	Heymaster\Command
		 */
		protected function createCommand()
		{
			return new Command;
		}
		
		
		
		/**
		 * @return	Heymaster\Config
		 */
		protected function createConfig()
		{
			return new Config;
		}
		
		
		
		/**
		 * @return	Heymaster\Config\RelNode
		 */
		protected function createRelNode($dir, $scope)
		{
			return RelNode::create($dir, $scope);
		}
		
		
		
		/**
		 * @return	array
		 */
		protected static function getDefaultParameters()
		{
			return array(
				'tag' => TRUE,
				'testingMode' => FALSE,
				'root' => FALSE,
				'builder' => 'git',
			);
		}
	}

