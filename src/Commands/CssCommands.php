<?php
	/** CSS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Commands\Css\ICssMinifier;
	
	class CssCommands extends CommandSet
	{
		const MASK = '*.css';
		
		/** @var  Heymaster\Commands\Css\ICssMinifier */
		private $minifier;
		
		
		
		public function __construct(ICssMinifier $minifier)
		{
			$this->minifier = $minifier;
		}
		
		
		
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
			
			foreach($this->findFiles($mask, $actionMask, $command->config->root) as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $this->minifier->minify($content));
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

