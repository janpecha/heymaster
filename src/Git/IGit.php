<?php
	/** IGit interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-11-1
	 */
	
	namespace Heymaster\Git;
	
	interface IGit
	{
		/** Create a tag.
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function tag($name);
		
		
		
		/**
		 * @param	string
		 * @param	array|NULL
		 * @throws	Heymaster\Git\GitException
		 */
		public function merge($branch, $options = NULL);
		
		
		
		/**
		 * @param	string
		 * @param	bool
		 * @throws	Heymaster\Git\GitException	
		 */
		public function branchCreate($name, $checkout = FALSE);
		
		
		
		/**
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function branchRemove($name);
		
		
		
		/** Returns name of current branch
		 * @return	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function branchName();
		
		
		
		/**
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function checkout($name);
		
		
		
		/** Removes file.
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function remove($file);
		
		
		
		/** Add file.
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function add($file);
		
		
		
		/**
		 * @param	string
		 * @param	string[]  param => value
		 * @throws	Heymaster\Git\GitException
		 */
		public function commit($message, $params = NULL);
		
		
		
		/**
		 * @return	bool
		 */
		public function isChanges();
	}
	
	
	
	class GitException extends \Exception
	{
	}

