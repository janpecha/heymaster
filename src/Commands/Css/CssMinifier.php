<?php
	/** Heymaster Default Css Minifier
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands\Css;
	
	class CssMinifier extends \CssMinifier implements ICssMinifier
	{
		public function minify($s)
		{
			return parent::minify($s);
		}
	}
	
