<?php
	/** Heymaster Commands PHP Shrink Factory Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands\Php;
	
	interface IPhpShrinkFactory
	{
		/**
		 * @return	IPhpShrink
		 */
		function createPhpShrink();
	}

