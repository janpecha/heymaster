<?php
	/** IGit interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-27-1
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
		 * @throws	Heymaster\Git\GitException
		 */
		public function merge($brach);
		
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
		
		/**
		 * @param	string
		 * @throws	Heymaster\Git\GitException
		 */
		public function checkout($name);
	}
	
	
	
	class GitException extends \Exception
	{
	}

