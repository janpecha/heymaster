<?php
	/** Heymaster Base Adapter
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-1
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
			KEY_RUNNABLE = 'run';
			
		/** @var  string[] */
		protected $warnings = array();
		
		
		
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
		 * @return	array
		 */
		public static function createConfiguration()
		{
			$config = array(
				'config' => new FileConfig,
				'sections' => array(
					self::SECTION_BEFORE => self::createSection(),
					self::SECTION_AFTER => self::createSection(),
				),
			);
			
			$config['config']->output = TRUE;
			
			$config['sections'][self::SECTION_BEFORE]->name = self::SECTION_BEFORE;
			$config['sections'][self::SECTION_AFTER]->name = self::SECTION_AFTER;
			
			return $config;
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
		
		
		
		/**
		 * @return	Heymaster\Section
		 */
		public static function createSection()
		{
			$section = new Section;
			$section->config = self::createConfig();
			
			return $section;
		}
	}

