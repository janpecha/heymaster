<?php
	/** Heymaster Default Css Minifier
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands\Css;
	
	class CssMinifier extends \CssMinifier implements ICssMinifier
	{
		public function minify($s)
		{
			return parent::minify($s);
		}
	}
	
