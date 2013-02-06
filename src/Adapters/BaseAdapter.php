<?php
	/** Heymaster Base Adapter
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-06-1
	 */
	
	namespace Heymaster\Adapters;
	
	use Heymaster\Config,
		Heymaster\Section,
		Heymaster\Action,
		Heymaster\Command,
		Heymaster\Configs\FileConfig;
	
	abstract class BaseAdapter extends \Nette\Object implements IAdapter
	{
		const SECTION_BEFORE = 'before',
			SECTION_AFTER = 'after';
		
		const KEY_ACTIONS = 'actions',
			KEY_RUNNABLE = 'run',
			KEY_PARAMETERS = 'parameters';
			
		/** @var  string[] */
		protected $warnings = array();
		
		/** @var  array */
		protected $configuration;
		
		
		
		public function load($file)
		{
			$this->configuration = self::createConfiguration();
		}
		
		
		
		// Warns
		/**
		 * @param	string
		 * @return	$this  fluent interface
		 */
		public function addWarning($msg)
		{
			$this->warnings[] = (string)$msg;
			return $this;
		}
		
		
		
		/**
		 * @return	string[]
		 */
		public function getWarnings()
		{
			return $this->warnings;
		}
		
		
		
		protected function addParameter($name, $value)
		{
			if(!isset($this->configuration[self::KEY_PARAMETERS]))
			{
				throw new AdapterException('Adapter neni pripraven, nelze pridat parametr.');
			}
			
			$parts = explode('.', $name);
			$first = array_shift($parts);
			$parent = &$this->configuration[self::KEY_PARAMETERS][$first];
			
			foreach($parts as $part)
			{
				$parent = &$parent[$part];
			}
			
			$parent = $value;
			return $this;
		}
		
		
		
		/**
		 * @return	array
		 */
		public static function createConfiguration()
		{
			return array(
				'root' => NULL,
				'inherit' => FALSE,
				'output' => FALSE,
				'parameters' => array(),
				'sections' => array(
					self::SECTION_BEFORE => array(),
					self::SECTION_AFTER => array(),
				),
			);
		}
		
		
		
		/**
		 * @return	Heymaster\Config
		 */
		public static function createConfig()
		{
			return new Config;
		}
		
		
		
		/**
		 * @return	Heymaster\Action
		 */
		public static function createAction()
		{
			$action = new Action;
			$action->config = self::createConfig();
			
			return $action;
		}
		
		
		
		/**
		 * @return	Heymaster\Command
		 */
		public static function createCommand()
		{
			$command = new Command;
			$command->config = self::createConfig();
			
			return $command;
		}
	}

