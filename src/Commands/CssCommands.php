<?php
	/** CSS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Commands\Css\ICssMinifier,
		Heymaster\Config\Configurator;
	
	class CssCommands extends CommandSet
	{
		const MASK = '*.css';
		
		/** @var  Heymaster\Commands\Css\ICssMinifier */
		private $minifier;
		
		
		
		public function __construct(ICssMinifier $minifier)
		{
			$this->minifier = $minifier;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function install(Configurator $configurator)
		{
			$configurator->addCommand('Css::compress', array($this, 'commandCompress'));
			
			return $this;
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

