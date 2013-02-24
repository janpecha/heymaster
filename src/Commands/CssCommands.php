<?php
	/** CSS Commands
	 * REQUIRE CssMinifier!
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException;
	
	class CssCommands extends CommandSet
	{
		const MASK = '*.css';
		
		
		
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('Css::compress', array($me, 'commandCompress'));
			
			return $me;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompress(Command $command, Config $config, $mask)
		{
			$mask = isset($command->params['mask']) ? $command->params['mask'] : self::MASK;
			$minifier = new \CssMinifier;
			
			foreach($this->findFiles($mask, $actionMask, $command->config->root) as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $minifier->minify($content));
			}
		}
		
		
		
		public function findFiles($mask, $actionMask, $root)
		{
			$finder = $this->heymaster->findFiles($mask)
				->mask($actionMask);
			
			$finder->from($root)
				->exclude('.git');
			
			return $finder;
		}
	}

