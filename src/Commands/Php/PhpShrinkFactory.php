<?php
	/** Heymaster PHPShrink Factory
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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
	
