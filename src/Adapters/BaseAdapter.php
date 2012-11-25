<?php
	/** Heymaster Base Adapter
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-25-2
	 */
	
	namespace Heymaster\Adapters;
	
	use Heymaster\Config,
		Heymaster\Section,
		Heymaster\Action,
		Heymaster\Command;
	
	abstract class BaseAdapter extends \Nette\Object implements IAdapter
	{
		const SECTION_BEFORE = 'before',
			SECTION_AFTER = 'after';
		
		const KEY_ACTIONS = 'actions';
		
		
		/**
		 * @return	array
		 */
		public static function createConfig()
		{
			$config = array(
				'config' => new Config,
				'sections' => array(
					self::SECTION_BEFORE => new Section,
					self::SECTION_AFTER => new Section,
				),
			);
			
			$config['sections'][self::SECTION_BEFORE]->name = $self::SECTION_BEFORE;
			$config['sections'][self::SECTION_AFTER]->name = $self::SECTION_AFTER;
			
			return $config;
		}
		
		
		
		/**
		 * @return	Heymaster\Action
		 */
		public static function createAction()
		{
			return new Action;
		}
		
		
		
		/**
		 * @return	Heymaster\Command
		 */
		public static function createCommand()
		{
			return new Command;
		}
	}

