<?php
	/** IGit interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-3
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
		 * @throws	Heymaster\Git\GitException
		 */
		public function commit($message);
	}
	
	
	
	class GitException extends \Exception
	{
	}

