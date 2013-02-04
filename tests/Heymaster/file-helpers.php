<?php
	/** Heymaster File Helpers for tests
	 * 
	 * @version		2013-02-04-1
	 */
	
	/** @author	David Grudl */
	function export($iterator)
	{
		$arr = array();
		foreach ($iterator as $key => $value) $arr[] = strtr($key, '\\', '/');
		sort($arr);
		return $arr;
	}
	
	
	
	/**
	 * Purges directory.
	 * @param  string
	 * @return void
	 * @author David Grudl
	 * @edited by Jan Pecha
	 */
	function purge($dir)
	{
		@mkdir($dir, 0777, TRUE); // @ - directory may already exist
		foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST) as $entry) {
			//if (substr($entry->getBasename(), 0, 1) === '.') { // . or .. or .gitignore
			$basename = $entry->getBasename();
		
			if($basename === '.' || $basename === '..') {
				// ignore
			} elseif ($entry->isDir()) {
				rmdir($entry);
			} else {
				unlink($entry);
			}
		}
	}
	
	
	
	function removeRoot($arr, $root)
	{
		$len = strlen($root);
	
		foreach($arr as &$value)
		{
			if(substr($value, 0, $len) === $root)
			{
				$value = substr($value, $len);
			}
		}
	
		return $arr;
	}

