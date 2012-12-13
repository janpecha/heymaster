<?php
	/** Core Commands
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-13-1
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
			
#			$heymaster->addCommand('Php::compress', array($me, 'commandCompress'));
			$heymaster->addCommand('Php::lint', array($me, 'commandLint'));
			
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
		
		
		
		public function findFiles($mask, $actionMask, $root)
		{
			$finder = $this->heymaster->findFiles($mask)
				->mask($actionMask);
			
			$finder->from($root)
				->exclude('.git');
			
			return $finder;
		}
	}

