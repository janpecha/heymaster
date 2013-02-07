<?php
	/** Heymaster Adapter Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-07-1
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
	
	class AdapterException extends \Exception
	{
	}

