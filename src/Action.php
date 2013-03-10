<?php
	/** Heymaster Action Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster;
	
	class Action extends \Nette\Object
	{
		/** @var  string */
		public $name;
		
		/** @var  bool */
		public $runnable = TRUE;
		
		/** @var  string|NULL TODO: ?? */
		public $mask;
		
		/** @var  Config */
		public $config;
		
		/** @var  Command[] */
		public $commands;
		
		
		
		public function process($scope, $config)
		{
			if(!is_array($this->commands) || !$this->runnable)
			{
				return;
			}
			
			$actionConfig = clone $this->config;
			$actionConfig->inherit($config);
			
			foreach($this->commands as $command)
			{
				$command->process($scope, $actionConfig, $this->mask);
			}
			
			return TRUE;
		}
	}

