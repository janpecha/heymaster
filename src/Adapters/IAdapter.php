<?php
	/** Heymaster Adapter Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-1
	 */
	
	namespace Heymaster\Adapters;
	
	interface IAdapter
	{
		/**
		 * @param	string
		 * @return	array|FALSE
		 * @todo	2012-11-25  melo by vyhazovat vyjimku
		 */
		public function load($filename);
	}
	
	class Exception extends \Exception
	{
	}

