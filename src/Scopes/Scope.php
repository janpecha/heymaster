<?php
	/** Heymaster Scope
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Scopes;
	use Nette,
		Heymaster\Logger\ILogger,
		Heymaster\InvalidException,
		Heymaster\Section,
		Nette\Config\Helpers;
	
	class Scope extends Nette\Object
	{
		/** @var  Heymaster\Scopes\Scope|NULL */
		private $parent;
		
		/** @var  Heymaster\Scopes\Scope[]  [(string)dirpath => (Scope) scope] */
		private $children = array();
		
		/** @var  Heymaster\Section|NULL */
		private $before;
		
		/** @var  Heymaster\Section|NULL */
		private $after;
		
		/** @var  bool */
		private $inherit = FALSE;
		
		/** @var  string */
		private $root; // TODO: nastaveni
		
		/** @var  string */
		private $workingRoot;
		
		/** @var  string */
		private $processRoot;
		
		/** @var  bool|NULL  NULL => inherit output, default NULL => FALSE */
		private $output;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  string[] */
		private $ignorePaths = array('.git');
		
		/** @var  array */
		private $parameters = array();
		
		
		
		/**
		 * @param	string
		 * @param	Heymaster\Logger\ILogger
		 */
		public function __construct($root, ILogger $logger)
		{
			$this->root = realpath($root);
			$this->logger = $logger;
			
			if($this->root === FALSE)
			{
				throw new InvalidException('Spatna cesta pro Scope - adresar neexistuje: ' . $root);
			}
			
			$this->workingRoot = $this->root;
		}
		
		
		
		/**
		 * @return	string|NULL
		 */
		public function getRoot()
		{
			return $this->root;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 * @throws	Heymaster\InvalidException
		 */
		public function setWorkingRoot($root)
		{
			if($root !== NULL)
			{
				$this->workingRoot = realpath($root);
			
				if($this->workingRoot === FALSE)
				{
					throw new InvalidException("Pracovni adresar neexistuje: $root");
				}
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	string
		 */
		public function getProcessRoot()
		{
			return rtrim($this->workingRoot . '/' . $this->processRoot, '/');
		}
		
		
		
		/**
		 * @param	string|NULL
		 * @return	$this
		 * @throws	Heymaster\InvalidException
		 */
		public function setProcessRoot($root)
		{
			if(!is_string($root) && !is_null($root))
			{
				throw new InvalidException('Naplatny process-root. Musi by typu string, nebo NULL.');
			}
			
			$this->processRoot = $root;
			return $this;
		}
		
		
		
		/**
		 * @return	Heymaster\Logger\ILogger
		 */
		public function getLogger()
		{
			return $this->logger;
		}
		
		
		
		/**
		 * @return	bool
		 */
		public function getOutput()
		{
			return $this->output;
		}
		
		
		
		/**
		 * @param	bool
		 * @return	$this
		 */
		public function setOutput($output = TRUE)
		{
			$this->output = (bool)$output;
			return $this;
		}
		
		
		
		/**
		 * @param	Scope
		 * @param	bool
		 * @return	$this
		 * @throws	Heymaster\InvalidException
		 */
		public function setParent(Scope $parent, $fatal = TRUE)
		{
			if(!$this->parent)
			{
				$this->parent = $parent;
				$this->parent->addChild($this);
			}
			else
			{
				if($fatal)
				{
					throw new InvalidException('Scope uz ma nastaveneho rodice.');
				}
			}
			
			return $this;
		}
		
		
		
		/**
		 * @return	bool
		 */
		public function getInherit()
		{
			return $this->inherit;
		}
		
		
		
		/**
		 * @param	bool
		 * @return	$this
		 */
		public function setInherit($inherit = TRUE)
		{
			$this->inherit = (bool)$inherit;
			return $this;
		}
		
		
		
		/**
		 * @param	Scope
		 * @return	$this
		 */
		public function addChild(Scope $child)
		{
			$directory = (string)$child->getRoot();
			if(!isset($this->children[$directory]))
			{
				$this->children[$directory] = $child;
				$child->setParent($this, FALSE);
			}
			
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Section
		 * @return	$this
		 */
		public function setBefore(Section $before)
		{
			if($this->before)
			{
				throw new InvalidException('Scope uz obsahuje sekci before.');
			}
			
			$this->before = $before;
			$this->before->scope = $this;
			return $this;
		}
		
		
		
		/**
		 * @param	Heymaster\Section
		 * @return	$this
		 */
		public function setAfter(Section $after)
		{
			if($this->after)
			{
				throw new InvalidException('Scope uz obsahuje sekci after.');
			}
			
			$this->after = $after;
			$this->after->scope = $this;
			return $this;
		}
		
		
		
		/**
		 * @param	array
		 * @return	$this
		 */
		public function addParameters(array $params)
		{
			$this->parameters = Helpers::merge($params, $this->parameters);
			return $this;
		}
		
		
		
		/**
		 * @return	array
		 */
		public function getParameters()
		{
			return $this->parameters;
		}
		
		
		
		/**
		 * @param	string  name of parameter
		 * @param	mixed|NULL  default value (optional parameter), NULL => required parameter
		 * @param	string|NULL  error message
		 * @return	mixed
		 */
		public function getParameter($name, $default = NULL, $message = NULL)
		{
			$name = (string)$name;
			if(!isset($this->parameters[$name]))
			{
				if($default !== NULL)
				{
					return $default;
				}
				
				throw new InvalidException($message !== NULL ? (string)$message : "Parameter $name is required.");
			}
			
			return $this->parameters[$name];
		}
		
		
		
		/**
		 * @return	void
		 */
		public function processBefore()
		{
			$this->logger->prefix('scope')
				->info('before - ' . $this->root);
			
			if($this->before)
			{
				$cwd = getcwd();
				chdir($this->getProcessRoot());
				
				$this->before->process();
				
				if($cwd !== FALSE)
				{
					chdir($cwd);
				}
			}
			else
			{
				$this->logger->info('Section is empty');
			}
			
			$this->logger->success('Done \'before\' section. ' . $this->root)
				->end();
			
			foreach($this->children as $dir => $child)
			{
				$child->setWorkingRoot($this->workingRoot . '/' . $this->removeRoot($dir));
				$child->processBefore();
			}
		}
		
		
		
		/**
		 * @return	void
		 */
		public function processAfter()
		{
			$this->logger->prefix('scope')
				->info('after - ' . $this->root);
				
			if($this->after)
			{
				$this->after->process();
			}
			else
			{
				$this->logger->info('Section is empty');
			}
			
			$this->logger->success('Done \'after\' section. ' . $this->root)
				->end();
		}
		
		
		
		/**
		 * @param	string|string[]|NULL
		 * @return	Heymaster\Scopes\FinderCreator
		 */
		public function findFiles($mask = NULL)
		{
			$creator = $this->createFinderCreator()
				->directory($this->getProcessRoot())
				->excludeDir($this->ignorePaths)
				->excludeFile($this->ignorePaths)
				->recursive();
				
			if($mask !== NULL)
			{
				$creator->files($mask);
			}
			
			foreach($this->children as $dir => $child)
			{
				$creator->excludeDir($this->removeRoot($dir));
			}
			
			return $creator;
		}
		
		
		
		/**
		 * @param	string|string[]|NULL
		 * @return	Heymaster\Scopes\FinderCreator
		 */
		public function findDirectories($mask = NULL)
		{
			$creator = $this->createFinderCreator()
				->directory($this->getProcessRoot())
				->excludeDir($this->ignorePaths)
				->excludeFile($this->ignorePaths)
				->recursive();
				
			if($mask !== NULL)
			{
				$creator->dirs($mask);
			}
			
			foreach($this->children as $dir => $child)
			{
				$creator->excludeDir($this->removeRoot($dir));
			}
			
			return $creator;
		}
		
		
		
		/**
		 * @return	Heymaster\Scopes\FinderCreator
		 */
		protected function createFinderCreator()
		{
			return new FinderCreator;
		}
		
		
		
		/**
		 * @param	string
		 * @return	string
		 */
		protected function removeRoot($value)
		{
			$root = $this->getRoot();
			$len = strlen($root);
	
			if(substr($value, 0, $len) === $root)
			{
				return substr($value, $len);
			}

			return FALSE;
		}
	}

