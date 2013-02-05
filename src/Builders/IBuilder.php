<?php
	/** Heymaster Builder Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-05-1
	 */
	
	namespace Heymaster\Builders;
	
	interface IBuilder
	{
		/**
		 * @param	bool
		 */
		function setTestingMode($testingMode);
		
		
		
		/**
		 * @param	string
		 * @param	Heymaster\Scopes\Scope
		 */
		function startup($tag, Scope $scope);
		
		
		
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

