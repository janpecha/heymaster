<?php
	/** Heymaster Simple Files Manipulator
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-02-1
	 */
	
	namespace Heymaster\Files;
	
	class SimpleManipulator extends \Nette\Object
	{
		/** @var  string */
		protected $rootDir;
		
		
		
		/**
		 * @param	string
		 */
		public function __construct($rootDir)
		{
			$this->rootDir = (string)$rootDir;
		}
		
		
		
		/**
		 * @param	string|NULL
		 * @author	David Grudl, 2009  (modified by Jan Pecha, 2012)
		 * @return	array  [(string)filepath => (int)0]
		 */
		public function getFileList($dir = NULL)	// ?ok
		{
			if($dir === NULL)
			{
				$dir = $this->rootDir;
			}
			elseif(is_string($dir) && $dir[0] !== '/') // relative paths
			{
				$dir = $this->rootDir . '/' . $dir;
			}
			
			$res = array();
			$iterator = dir($dir);
			
			while(FALSE !== ($entry = $iterator->read()))
			{
				$path = "$dir/$entry";
				
				if($entry == '.' || $entry == '..' || $entry == '.git')
				{
					continue;
				}
				elseif(!is_readable($path))
				{
					continue;
				}
				#elseif($this->matchMask($path, $this->ignoreMasks))
				#{
				#	//$this->logger->log("Ignoring $path");
				#	continue;
				#}
				elseif(is_dir($path))
				{
					$res["$dir/$entry/"] = 0;//TRUE;
					$res += $this->getFileList("$dir/$entry");
				}
				elseif(is_file($path))
				{
					$res["$dir/$entry"] = 0;//md5_file($this->preprocess($path));
				}
			}
			
			$iterator->close();
			 
			return $res;
		}
		
		
		
		/**
		 * @param	array  of files
		 * @param	bool
		 * @param	callback|FALSE
		 * @return	void
		 */
		public function removeFiles($list, $useKey = TRUE, $callback = FALSE) // ??ok
		{
			if($callback === FALSE)
			{
				$callback = 'unlink';
			}
			
			if(!$useKey)
			{
				$list = array_flip($list);
			}
			
			foreach($list as $file => $value)
			{
				if(file_exists($file))
				{
					//$this->git->remove($file);
					call_user_func($callback, $file);
				}
			}
		}
	}

