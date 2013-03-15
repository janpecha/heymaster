<?php
	/** Heymaster Action Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster;
	use Heymaster\Scopes\Scope;
	
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
		
		
		
		/**
		 * @param	Heymaster\Scopes\Scope
		 * @param	Heymaster\Config
		 * @return	TRUE|NULL
		 */
		public function process(Scope $scope, Config $config)
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

