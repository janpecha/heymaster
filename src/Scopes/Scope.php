<?php
	/** Heymaster Scope
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-05-1
	 */
	
	namespace Heymaster\Scopes;
	use Nette,
		Heymaster\Logger\ILogger,
		Heymaster\InvalidException,
		Heymaster\Section;
	
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
		
		/** @var  bool|NULL  NULL => inherit output */
		private $output;
		
		/** @var  Heymaster\Logger\ILogger */
		private $logger;
		
		/** @var  string[] */
		private $ignorePaths = array('.git*');
		
		/** @var  bool */
		private $testingMode = FALSE;
		
		
		
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
		
		
		
		public function isTestingMode()
		{
			return $this->testingMode;
		}
		
		
		
		public function setTestingMode($testingMode = TRUE)
		{
			$this->testingMode = (bool)$testingMode;
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
			return $this;
		}
		
		
		
		public function setAfter(Section $after)
		{
			if($this->after)
			{
				throw new InvalidException('Scope uz obsahuje sekci after.');
			}
			
			$this->after = $after;
			return $this;
		}
		
		
		
		/**
		 * @return	void
		 */
		public function process()
		{
			$this->logger->prefix('scope')
				->info($this->root);
			
			if($this->before)
			{
				$this->before->process();
			}
			
			if(!$this->testingMode && $this->after)
			{
				$this->after->process();
			}
			
			$this->logger->success('Done. ' . $this->root)
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

