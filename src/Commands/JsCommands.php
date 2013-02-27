<?php
	/** JS Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-24-1
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\Config,
		Heymaster\InvalidException,
		Heymaster\Commands\Js\IJsShrink;
	
	class JsCommands extends CommandSet
	{
		const MASK = '*.js';
		
		/** @var  Heymaster\Commands\Js\IJsShrink */
		private $jsShrink;
		
		
		
		public function __construct(IJsShrink $jsShrink)
		{
			$this->jsShrink = $jsShrink;
		}
		
		
		
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('Js::compress', array($me, 'commandCompress'));
			$heymaster->addCommand('Js::compile', array($me, 'commandCompile'));
			// TODO: Js::hint
			
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
			$maskParam = $command->getParameter('mask', self::MASK);
			$creator = $command->findFiles($maskParam)
				->recursive();
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, $this->jsShrink($content));
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	Heymaster\Config
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompile(Command $command, Config $config, $mask)
		{
			$maskParam = $command->getParameter('mask', self::MASK);
			$recursive = (bool) $command->getParameter('recursive', TRUE);
			$filename = (string) $command->getParameter('file', NULL, 'Js::compile: Neni urceno jmeno souboru, do ktereho se ma kompilovat.');
			$creator = $command->findFiles($maskParam)
				->recursive($recursive);
			
			$delimiter = '';
			$filename = $config->root . '/' . $filename;
			file_put_contents($filename, '');
			
			foreach($creator->find() as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($filename, $delimiter . jsShrink($content), \FILE_APPEND);
				$delimeter = "\n;";
			}
		}
	}

