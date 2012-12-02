<?php
	/** Heymaster Base Adapter
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-02-1
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
		
		
		/**
		 * @return	array
		 */
		public static function createConfiguration()
		{
			$config = array(
				'config' => new FileConfig,
				'sections' => array(
					self::SECTION_BEFORE => new Section,
					self::SECTION_AFTER => new Section,
				),
			);
			
			$config['sections'][self::SECTION_BEFORE]->name = self::SECTION_BEFORE;
			$config['sections'][self::SECTION_AFTER]->name = self::SECTION_AFTER;
			
			return $config;
		}
		
		
		
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

