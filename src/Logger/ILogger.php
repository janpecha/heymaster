<?php
	/** ILogger interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-15-1
	 */
	
	interface ILogger
	{
		public function log($str);
		public function error($str);
		public function success($str);
		public function warn($str);
		public function info($str);
	}

