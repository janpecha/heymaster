<?php
	/** Heymaster Commands PHP Shrink Factory Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Php;
	
	interface IPhpShrinkFactory
	{
		/**
		 * @return	IPhpShrink
		 */
		function createPhpShrink();
	}

