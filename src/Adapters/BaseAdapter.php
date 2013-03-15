<?php
	/** Heymaster Base Adapter
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
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
			KEY_PARAMETERS = 'parameters',
			KEY_ROOT = 'root',
			KEY_OUTPUT = 'output',
			KEY_MESSAGE = 'message',
			KEY_INHERIT = 'inherit',
			KEY_SECTIONS = 'sections',
			KEY_COMMANDS = 'commands',
			KEY_NAME = 'name',
			KEY_DESCRIPTION = 'description',
			KEY_PARAMS = 'params',
			KEY_MASK = 'mask';
			
		/** @var  string[] */
		protected $warnings = array();
		
		/** @var  array */
		protected $configuration;
		
		
		
		/**
		 * @param	string
		 * @return	array|FALSE
		 */
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
		
		
		
		/**
		 * @param	string   'name-of-parameter' OR 'parent.child.parameter'
		 * @param	mixed
		 * @return	$this
		 */
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
				self::KEY_ROOT => NULL,
				self::KEY_INHERIT => FALSE,
				self::KEY_OUTPUT => FALSE,
				self::KEY_MESSAGE => FALSE,
				self::KEY_PARAMETERS => array(),
				self::KEY_SECTIONS => array(
					self::SECTION_BEFORE => self::createSection(),
					self::SECTION_AFTER => self::createSection(),
				),
			);
		}
		
		
		
		/**
		 * @return	array
		 */
		public static function createSection()
		{
			return array(
				self::KEY_ROOT => NULL,
				self::KEY_OUTPUT => NULL,
				self::KEY_MESSAGE => NULL,
				self::KEY_ACTIONS => array(),
			);
		}
		
		
		
		/**
		 * @return	array
		 */
		public static function createAction()
		{
			return array(
				self::KEY_ROOT => NULL,
				self::KEY_OUTPUT => NULL,
				self::KEY_MESSAGE => NULL,
				self::KEY_RUNNABLE => TRUE,
				self::KEY_MASK => NULL,
				self::KEY_COMMANDS => array(),
			);
		}
		
		
		
		/**
		 * @return	array
		 */
		public static function createCommand()
		{
			return array(
				self::KEY_NAME => NULL,
				self::KEY_DESCRIPTION => FALSE,
				self::KEY_ROOT => NULL,
				self::KEY_OUTPUT => NULL,
				self::KEY_MESSAGE => NULL,
				self::KEY_PARAMS => array(),
			);
		}
	}

