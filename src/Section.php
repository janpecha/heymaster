<?php
	/** Heymaster Action Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-11-1
	 */
	
	namespace Heymaster;
	
	class Section extends \Nette\Object
	{
		/** @var  string|FALSE */
		public $name;
		
		/** @var  Heymaster\Config */
		public $config;
		
		/** @var  Heymaster\Actions[] */
		public $actions;
		
		/** @var  Heymaster\Scopes\Scope */
		public $scope;
		
		
		
		public function process()
		{
			if(!is_array($this->actions))
			{
				return;
			}
			
			$clonedConfig = clone $this->config;
			$clonedConfig->inherit('root', $this->scope->getRoot());
			$clonedConfig->inherit('output', $this->scope->getOutput());
			
			foreach($this->actions as $action)
			{
				$action->process($this->scope, $clonedConfig);
			}
			
			return TRUE;
		}
	}

