<?php
	/** Heymaster Command Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-26-1
	 */
	
	namespace Heymaster;
	use Heymaster\Scopes\Scope,
		Heymaster\Config,
		RuntimeException;
	
	class Command extends \Nette\Object
	{
		/** @var  string|FALSE */
		public $name;
		
		/** @var  string|FALSE optional */
		public $description = FALSE;
		
		/** @var  callback */
		public $callback;
		
		/** @var  array */
		public $params;
		
		/** @var  Config */
		public $config;
		
		/** @var  Heymaster\Scopes\Scope */
		private $scope;
		
		/** @var  bool */
		private $processing = FALSE;
		
		/** @var  string|string[]|NULL */
		private $processMask;
		
		/** @var  Heymaster\Config */
		private $processConfig;
		
		
		
		/**
		 * @return	Heymaster\Scopes\Scope|NULL
		 */
		public function getScope()
		{
			return $this->scope;
		}
		
		
		
		/**
		 * @param	Heymaster\Scopes\Scope
		 * @return	$this
		 */
		public function setScope(Scope $scope)
		{
			if($scope !== NULL)
			{
				throw new RuntimeException('Command: Scope uz je nastaven.');
			}
			
			$this->scope = $scope;
			return $this;
		}
		
		
		
		/**
		 * @param	string  name of parameter
		 * @param	mixed|NULL  default value (optional parameter), NULL => required parameter
		 * @param	string|NULL  error message
		 * @return	mixed
		 */
		public function getParameter($name, $default = NULL, $message = NULL)
		{
			foreach((array) $name as $key)
			{
				if(isset($this->params[$key]))
				{
					return $this->params[$key];
				}
			}
			
			if($default !== NULL)
			{
				return $default;
			}
			
			throw new InvalidException($message !== NULL ? (string)$message : "Parameter $name is required.");
		}
		
		
		
		public function process(Scope $scope, Config $config, $mask)
		{
			$this->processConfig = clone $this->config;
			$this->processConfig->inherit($config);
			
			$this->processMask = $mask;
			
			$this->setScope($scope);
			
			/* command, config, mask */
			$this->processing = TRUE;
			call_user_func($this->callback, $this, $this->processConfig, $this->processMask);
			$this->processing = FALSE;
		}
		
		
		
		/**
		 * @param	string|string[]|NULL
		 * @return	Heymaster\Scopes\FinderCreator
		 */
		public function findFiles($masks = NULL)
		{
			if(!$this->processing)
			{
				throw new RuntimeException('Command neni zpracovavan.');
			}
			
			if(!$this->scope)
			{
				throw new RuntimeException('Command: scope not set.');
			}
			
			if(!is_array($masks) && $masks !== NULL)
			{
				$masks = func_get_args();
			}
			
			return $this->scope->findFiles($masks)
				->files($tihs->processMask)
				->directory($this->processConfig->root);
		}
		
		
		
		/**
		 * @param	string|string[]|NULL
		 * @return	Heymaster\Scopes\FinderCreator
		 */
		public function findDirectories($masks = NULL)
		{
			if(!$this->processing)
			{
				throw new RuntimeException('Command neni zpracovavan.');
			}
			
			if(!$this->scope)
			{
				throw new RuntimeException('Command: scope not set.');
			}
			
			if(!is_array($masks) && $masks !== NULL)
			{
				$masks = func_get_args();
			}
			
			return $this->scope->findDirectories($masks)
				->dirs($this->processMask) // TODO: ?? OK ??
				->directory($this->processConfig->root);
		}
	}

