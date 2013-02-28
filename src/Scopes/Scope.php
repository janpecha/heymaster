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
		private $processRoot;
		
		/** @var  bool|NULL  NULL => inherit output, default NULL => FALSE */
		private $output;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  string[] */
		private $ignorePaths = array('.git');
		
		/** @var  array */
		private $parameters = array();
		
		
		
		public function __construct($root, ILogger $logger)
		{
			$this->root = realpath($root);
			$this->logger = $logger;
			
			if($this->root === FALSE)
			{
				throw new InvalidException('Spatna cesta pro Scope - adresar neexistuje: ' . $root);
			}
		}
		
		
		
		public function getRoot()
		{
			return $this->root;
		}
		
		
		
		public function getProcessRoot()
		{
			return rtrim($this->root . '/' . $this->processRoot, '/');
		}
		
		
		
		public function setProcessRoot($root)
		{
			if(!is_string($root))
			{
				throw new InvalidException('Naplatny process-root. Musi to by string.');
			}
			
			$this->processRoot = $root;
		}
		
		
		
		public function getLogger()
		{
			return $this->logger;
		}
		
		
		
		public function getOutput()
		{
			return $this->output;
		}
		
		
		
		public function setOutput($output = TRUE)
		{
			$this->output = (bool)$output;
			return $this;
		}
		
		
		
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
		
		
		
		public function getInherit()
		{
			return $this->inherit;
		}
		
		
		
		public function setInherit($inherit = TRUE)
		{
			$this->inherit = (bool)$inherit;
			return $this;
		}
		
		
		
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
		
		
		
		public function addParameters(array $params)
		{
			$this->parameters = Helpers::merge($params, $this->parameters);
			return $this;
		}
		
		
		
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
				$this->before->process();
			}
			else
			{
				$this->logger->info('Section is empty');
			}
			
			$this->logger->success('Done \'before\' section. ' . $this->root)
				->end();
			
			foreach($this->children as $child)
			{
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
		
		
		
		public function findFiles($mask = NULL)
		{
			$creator = $this->createFinderCreator()
				->directory($this->root)
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
		
		
		
		public function findDirectories($mask = NULL)
		{
			$creator = $this->createFinderCreator()
				->directory($this->root)
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
		
		
		
		protected function createFinderCreator()
		{
			return new FinderCreator;
		}
		
		
		
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

