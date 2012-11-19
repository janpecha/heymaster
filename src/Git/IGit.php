<?php
	/** IGit interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-19-1
	 */
	
	namespace Heymaster\Git;
	
	interface IGit
	{
		public function tag($name);
		public function merge($brach);
		public function branchCreate($name, $checkout = FALSE);
		public function branchRemove($name);
		public function checkout($name);
	}

