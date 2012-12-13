<?php
	/** Php Commands
	 * REQUIRE PhpShrink!
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-13-2
	 */
	
	namespace Heymaster\Commands;
	
	use Nette\Object,
		Heymaster\Command,
		Heymaster\InvalidException;
	
	class PhpCommands extends CommandSet
	{
		const MASK = '*.php';
		
		
		
		public static function install(\Heymaster\Heymaster $heymaster)
		{
			$me = new static($heymaster);
			
			$heymaster->addCommand('Php::lint', array($me, 'commandLint'));
			$heymaster->addCommand('Php::compress', array($me, 'commandCompress'));
			$heymaster->addCommand('Php::compile', array($me, 'commandCompile'));
			
			return $me;
		}
		
		
		
		/**
		 * @param	Heymaster\Command
		 * @param	string
		 * @throws	Heymaster\InvalidException
		 * @return	void
		 */
		public function commandLint(Command $command, $actionMask)
		{
			$mask = isset($command->params['mask']) ? $command->params['mask'] : self::MASK;
			$error = FALSE;
			$output = (bool)$command->config->output;
			
			foreach($this->findFiles($mask, $actionMask, $command->config->root) as $file)
			{
				$ret = $this->heymaster->runner->run(array(
					'php -l',
					'-f' => (string)$file,
				), $output);
				
				if($ret !== 0)
				{
					$this->heymaster->logger->error('PhpLint: ' . $file);
					$error = TRUE;
				}
			}
			
			if($error)
			{
				throw new InvalidException('Php::lint: Any invalid files.');
			}
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
				$shrink = self::createPhpShrink();
				$shrink->addFile((string)$file);
				
				file_put_contents((string)$file, $shrink->getOutput());
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
			$mask = isset($command->params['mask']) ? $command->params['mask'] : self::MASK;
			
			if(isset($command->params['file']) && !is_string($command->params['file']))
			{
				throw new InvalidException('Nebylo zadano jmeno souboru, do ktereho se ma kompilovat.');
			}
			
			$outputFile = $command->params['file'];
			$shrink = self::createPhpShrink();
			
			foreach($this->findFiles($mask, $actionMask, $command->config->root) as $file)
			{
				$shrink->addFile((string)$file);
			}
			
			file_put_contents($command->config->root . '/' . $file, $shrink->getOutput());
		}
		
		
		
		public function findFiles($mask, $actionMask, $root)
		{
			$finder = $this->heymaster->findFiles($mask)
				->mask($actionMask);
			
			$finder->from($root)
				->exclude('.git');
			
			return $finder;
		}
		
		
		
		protected static function createPhpShrink()
		{
			$shrink = new PhpShrink;
			$shrink->useNamespaces = TRUE;
			
			return $shrink;
		}
	}

