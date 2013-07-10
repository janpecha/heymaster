<?php
	/** Heymaster File & Directory Finder
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Utils;
	
	use \Nette\Utils\Finder as NFinder;
	
	class Finder extends NFinder
	{
		/**
		 * @param	string|string[]
		 * @return	void
		 */
		public function mask($masks)
		{
			if(!is_array($masks))
			{
				$masks = func_get_args();
			}
			
			$pattern = self::ownBuildPattern($masks);
			
			if($pattern)
			{
				$this->filter(function($file) use ($pattern) {
					return !$file->isDot()
						&& (!$pattern || preg_match($pattern, '/' . strtr($file->getSubPathName(), '\\', '/')));
				});
			}
			
			return $this;
		}
		
		
		
		/**
		 * Converts Finder pattern to regular expression.
		 * @param	array
		 * @return	string
		 * @author	David Grudl
		 * @link	http://api.nette.org/2.0.7/source-Utils.Finder.php.html#176
		 */
		protected static function ownBuildPattern($masks)
		{
			$pattern = array();
			// TODO: accept regexp
			foreach ($masks as $mask)
			{
				$mask = rtrim(strtr($mask, '\\', '/'), '/');
				$prefix = '';
				
				if ($mask === '')
				{
					continue;
				}
				elseif($mask === '*')
				{
					return NULL;
				}
				elseif($mask[0] === '/') // absolute fixing
				{
					$mask = ltrim($mask, '/');
					$prefix = '(?<=^/)';
				}
				
				$pattern[] = $prefix . strtr(preg_quote($mask, '#'),
					array('\*\*' => '.*', '\*' => '[^/]*', '\?' => '[^/]', '\[\!' => '[^', '\[' => '[', '\]' => ']', '\-' => '-')
				);
			}
			
			return $pattern ? '#/(' . implode('|', $pattern) . ')\z#i' : NULL;
		}
	}

