<?php
	/** JS Commands
	 * REQUIRE JsShrink!
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-18-2
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\InvalidException;
	
	class JsCommands extends CommandSet
	{
		const MASK = '*.js';
		
		
		
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('Js::compress', array($me, 'commandCompress'));
			$heymaster->addCommand('Js::compile', array($me, 'commandCompile'));
			
			return $me;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompress(Command $command, $actionMask)
		{
			$mask = isset($command->params['mask']) ? $command->params['mask'] : self::MASK;
			
			foreach($this->findFiles($mask, $actionMask, $command->config->root) as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($file, jsShrink($content));
			}
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandCompile(Command $command, $actionMask)
		{
			if(!isset($command->params['file']))
			{
				throw new InvalidException('Neni urceno jmeno souboru, do ktereho se ma kompilovat.');
			}
			
			$mask = isset($command->params['mask']) ? $command->params['mask'] : self::MASK;
			$recursive = isset($command->params['recursive']) ? (bool)$command->params['recursive'] : TRUE;
			$filename = $command->params['file'];
			
			$delimiter = '';
			$filename = $command->config->root . '/' . $filename;
			file_put_contents($filename, '');
			
			foreach($this->findFilesForMerge($mask, $actionMask, $command->config->root, $recursive) as $file)
			{
				$content = file_get_contents($file);
				file_put_contents($filename, $delimiter . jsShrink($content), \FILE_APPEND);
				$delimeter = "\n;";
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
		
		
		
		/**
		 * @param	string|string[]
		 * @param	string|string[]
		 * @param	string
		 * @param	bool
		 * @return	Heymaster\Utils\Finder
		 */
		protected function findFilesForMerge($masks, $actionMasks, $root, $recursive = TRUE)
		{
			$finder = $this->heymaster->findFiles($masks)
				->mask($actionMasks);
			
			if($recursive)
			{
				$finder->from($root);
			}
			else
			{
				$finder->in($root);
			}
			
			$finder->exclude('.git');
			
			return $finder;
		}
	}

