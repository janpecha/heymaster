<?php
	/** Heymaster Finder Creator
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Scopes;
	use Heymaster\Utils\Finder,
		Nette;
	
	class FinderCreator extends Nette\Object
	{
		/** @var  string[] */
		protected $fileMasks = array();
		
		/** @var  callback[] */
		protected $fileFilters = array();
		
		/** @var  string[] */
		protected $fileExclude = array();
		
		/** @var  string[] */
		protected $dirMasks = array();
		
		/** @var  callback[] */
		protected $dirFilters = array();
		
		/** @var  string[] */
		protected $dirExclude = array();
		
		/** @var  string[] */
		protected $directories;
		
		/** @var  bool */
		protected $recursive = FALSE;
		
		/** @var  int */
		protected $maxDepth = -1;
		
		/** @var  bool */
		protected $childFirst = FALSE;
		
		
		
		/**
		 * @param	int
		 * @return	$this
		 */
		public function limitDepth($depth)
		{
			$this->maxDepth = (int)$depth;
			return $this;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function childFirst()
		{
			$this->childFirst = TRUE;
			return $this;
		}
		
		
		
		/** Prida masku/y pro filtrovani adresaru. Vice volani pridava masky.
		 * @param	string|string[]
		 * @return	$this
		 */
		public function dirs($masks)
		{
			if(!is_array($masks))
			{
				$masks = func_get_args();
			}
			
			$this->dirMasks = array_merge($this->dirMasks, $masks);
			return $this;
		}
		
		
		
		/** Prida masku/y pro filtrovani souboru. Vice volani pridava masky.
		 * @param	string|string[]
		 * @return	$this
		 */
		public function files($masks)
		{
			if(!is_array($masks))
			{
				$masks = func_get_args();
			}
			
			$this->fileMasks = array_merge($this->fileMasks, $masks);
			return $this;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	$this
		 */
		public function excludeFile($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			$this->fileExclude = array_merge($this->fileExclude, $mask);
			return $this;
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	$this
		 */
		public function excludeDir($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			$this->dirExclude = array_merge($this->dirExclude, $mask);
			return $this;
		}
		
		
		
		/** Prida filtr pro filtrovani souboru/adresaru. Vice volani pridava filtery.
		 * @param	callback
		 * @param	bool
		 * @return	$this
		 */
		public function filter($callback, $fileFilter = TRUE)
		{
			if($fileFilter)
			{
				$this->fileFilters[] = $callback;
			}
			else
			{
				$this->dirFilters[] = $callback;
			}
			
			return $this;
		}
		
		
		
		public function directory($dir)
		{
			if(!is_array($dir))
			{
				$dir = func_get_args();
			}
			
			$this->directories = $dir;
			return $this;
		}
		
		
		
		/** Prohledavat adresare rekurzivne?
		 * @param	bool
		 * @return	$this
		 */
		public function recursive($recursive = TRUE)
		{
			$this->recursive = (bool)$recursive;
			return $this;
		}
		
		
		
		/** Finds files
		 * @return	Heymaster\Utils\Finder
		 */
		public function find()
		{
			return $this->createFinder();
		}
		
		
		
		/** Finds directories
		 * @return	Heymaster\Utils\Finder
		 */
		public function findDirectories()
		{
			return $this->createFinderDirectories();
		}
		
		
		
		protected function createFinder()
		{
			$finder = Finder::findFiles($this->fileMasks);
			$finder->exclude($this->fileExclude);
			self::applyFilters($finder, $this->fileFilters);
			
			if($this->recursive)
			{
				$finder->from($this->directories);
				$finder->limitDepth($this->maxDepth);
			}
			else
			{
				$finder->in($this->directories);
			}
			
			$finder->mask($this->dirMasks);
			$finder->exclude($this->dirExclude);
			self::applyFilters($finder, $this->dirFilters);
			
			if($this->childFirst)
			{
				$finder->childFirst();
			}
			
			return $finder;
		}
		
		
		
		protected function createFinderDirectories()
		{
			$finder = Finder::findDirectories($this->dirMasks);
			$finder->exclude($this->dirExclude);
			self::applyFilters($finder, $this->dirFilters);
			
			if($this->recursive)
			{
				$finder->from($this->directories);
				$finder->limitDepth($this->maxDepth);
			}
			else
			{
				$finder->in($this->directories);
			}
			
			$finder->exclude($this->dirExclude);
			
			if($this->childFirst)
			{
				$finder->childFirst();
			}
			
			return $finder;
		}
		
		
		
		protected static function applyFilters($finder, $filters)
		{
			foreach($filters as $filter)
			{
				$finder->filter($filter);
			}
		}
	}

