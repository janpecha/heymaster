<?php
	/** Heymaster File Manipulator
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Files;
	use Nette,
		Heymaster\Utils\Finder;
	
	class FileManipulator extends Nette\Object
	{
		/** Removes symlink, file or directory.
		 * @param	string
		 * @param	bool
		 * @return	void
		 */
		public function unlink($file, $onlyContent = FALSE)
		{
			$file = (string)$file;
			
			if(!file_exists($file))
			{
				return;
			}
			
			if(is_link($file))
			{
				// remove link - see end of method => 'unlink($file);'
			}
			elseif(is_file($file))
			{
				if($onlyContent)
				{
					file_put_contents((string)$file, '');
					return;
				}
			}
			elseif(is_dir($file))
			{
				// browse child
				foreach($this->find('*')->in($file)->childFirst() as $item)
				{
					$this->unlink($item, FALSE);
				}
				
				if($onlyContent)
				{
					return;
				}
				
				rmdir($file);
				return;
			}
			
			unlink($file);
		}
		
		
		
		/**
		 * @param	string	from
		 * @param	string	to
		 * @param	string|string[] list of names to ignore
		 * @param	bool
		 * @return	void
		 */
		public function copy($from, $to, $ignore = NULL, $copyMode = FALSE)
		{
			if($ignore === NULL)
			{
				$ignore = array('.git');
			}
			elseif(is_string($ignore))
			{
				$ignore = array($ignore);
			}
			elseif(!is_array($ignore))
			{
				$ignore = array();
			}
			
			if(in_array(basename($from), $ignore))
			{
				return;
			}
			
			// TODO: co se symlinky, ted se symlinky nezachovaji, ale zkopiruje se to, na co ukazuji - je to OK??
			if(is_file($from))
			{
				@mkdir(dirname($to), 0777, TRUE); // adresar uz muze existovat
				copy($from, $to);
				
				if($copyMode)
				{
					$this->chmod($to, $from);
				}
			}
			elseif(is_dir($from))
			{
				@mkdir($to, 0777, TRUE); // adresar uz muze existovat
				
				if($copyMode)
				{
					$this->chmod($to, $from);
				}
				//$this->unlink($to, TRUE); // remove content TODO: is OK?
				
				foreach($this->find('*')->in($from) as $file)
				{
					$this->copy((string)$file, $to . '/' . $file->getBasename(), $ignore);
				}
			}
		}
		
		
		
		/**
		 * @param	string
		 * @param	string|int	string => by file|int => 0777, etc.
		 * @return	bool
		 */
		public function chmod($file, $mode = 0777)
		{
			if(is_string($mode))
			{
				$mode = $this->getmod($mode);
			}
			
			return chmod($file, $mode);
		}
		
		
		
		/**
		 * @param	string
		 * @return	int
		 */
		public function getmod($file)
		{
			return octdec('0' . substr(sprintf('%o', fileperms($file)), -3));
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function find($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::find($mask);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function findFiles($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findFiles($mask);
		}
		
		
		
		/**
		 * @param	string|string[]
		 * @return	Heymaster\Utils\Finder
		 */
		public function findDirectories($mask)
		{
			if(!is_array($mask))
			{
				$mask = func_get_args();
			}
			
			return Finder::findDirectories($mask);
		}
		
		
		
		/**
		 * @param	SplFileInfo
		 * @return	bool
		 */
		public static function isLink($file)
		{
			return $file->isLink();
		}
	}

