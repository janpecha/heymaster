<?php
	/** Heymaster Builder Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-1
	 */
	
	namespace Heymaster\Builders;
	
	use Heymaster\Configs\FileConfig;
	
	interface IBuilder
	{
		/**
		 * @param	bool
		 */
		public function setTestingMode($active);
		
		
		
		/**
		 * @param	string
		 * @param	Heymaster\Configs\FileConfig
		 */
		public function startup($tag, FileConfig $fileConfig);
		
		
		
		public function finish();
	}
	
	
	
	class Exception extends \Exception
	{
	}
