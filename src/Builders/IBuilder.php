<?php
	/** Heymaster Builder Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Builders;
	
	interface IBuilder
	{
		/**
		 * @param	array
		 */
		function setParameters(array $parameters);
		
		
		
		/**
		 * @return	string
		 */
		function getWorkingRoot();
		
		
		
		/**
		 * @param	string
		 */
		function setRoot($root);
		
		
		
		/**
		 * @param	bool
		 */
		function setTestingMode($testingMode);
		
		
		
		/**
		 * @param	string|bool  string => name|TRUE => auto tag|FALSE => no tag
		 */
		function startup($tag);
		
		
		
		/**
		 * @return	void
		 */
		function preprocess();
		
		
		
		/**
		 * Spusteno po zpracovani sekce 'before', ale pred sekci 'after'
		 */
		function postprocess();
		
		
		
		/**
		 * @return	void
		 */
		function finish();
	}
	
	
	
	class BuilderException extends \Exception
	{
	}

