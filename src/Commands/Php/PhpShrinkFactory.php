<?php
	/** Heymaster Default Css Minifier
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Php;
	use Nette;
	
	class PhpShrinkFactory extends Nette\Object implements IPhpShrinkFactory
	{
		/**
		 * @return	IPhpShrink
		 */
		public function createPhpShrink()
		{
			return new PhpShrink; // Heymaster\Commands\Php\PhpShrink
		}
	}
	
