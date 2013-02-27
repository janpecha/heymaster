<?php
	/** CSS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-2
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
			// promenna $mask je v Command::findFiles() pouzita automaticky
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $this->minifier->minify($content));
			}
		}
	}

