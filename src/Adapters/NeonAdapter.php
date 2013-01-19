<?php
	/** Heymaster Adapter Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-19-2
	 */
	
	namespace Heymaster\Adapters;
	
	use Heymaster\Config,
		Heymaster\Section,
		Heymaster\Action,
		Heymaster\Adapters\Exception,
		Heymaster\ConfigUnknowException;
	
	class NeonAdapter extends BaseAdapter
	{
		/**
		 * @param	string
		 * @return	array|FALSE
		 * @todo	2012-11-25  melo by vyhazovat vyjimku
		 */
		public function load($filename)
		{
			$res = \Neon::decode(file_get_contents($filename));
			
			if(is_array($res))
			{
				return $this->process($res);
			}
			
			return FALSE;
		}
		
		
		
		/**
		 * @param	array
		 * @return	array|FALSE
		 */
		protected function process(array $array)
		{
			$res = self::createConfiguration();
			
			foreach($array as $key => $value)
			{
				if($key === self::SECTION_BEFORE || $key === self::SECTION_AFTER)
				{
					if(is_array($value))
					{
						$this->processSection($value, $res['sections'][$key]);
					}
					elseif($value !== NULL)
					{
						throw new \UnexpectedValueException("Sekce '$key' neni validni.");
					}
				}
				else
				{
					$res['config']->set($key, $value);
				}
			}
			
			return $res;
		}
		
		
		
		/** Zpracuje $array (sekci 'before' (resp. 'after') z configu) a vysledek ulozi do $section
		 * 
		 * @param	array
		 * @return	void
		 */
		protected function processSection(array $array, Section $section)
		{
			foreach($array as $key => $value)
			{
				try
				{
					$section->config->set($key, $value);
				}
				catch(ConfigUnknowException $e)
				{
					if(isset($section->actions[$key]))
					{
						throw new Exception("Zdvojeny klic - akce '$key' je v konfiguracnim souboru uvedena dvakrat.");
					}
					elseif($value === NULL) // empty value (NULL) is ignored
					{
						$this->addWarning("Akce '$key' v sekci '{$section->name}' je prazdna.");
						continue;
					}
					elseif(!is_array($value))
					{
						throw new Exception("Akce '$key' v sekci '{$section->name}' neni validni.");
					}
					
					$section->actions[$key] = $this->processAction($key, $value);
				}
			}
		}
		
		
		
		/**
		 * @param	string
		 * @param	array
		 * @return	Heymaster\Action
		 */
		protected function processAction($name, array $array)
		{
			$action = self::createAction();
			$action->name = (string)$name;
			
			foreach($array as $key => $value)
			{
				if($key === self::KEY_ACTIONS)
				{
					if(!is_array($value))
					{
						throw new Exception("Akce '$name' neobsahuje zadne prikazy, nebo jsou prikazy chybne zapsany.");
					}
					
					$action->commands = $this->processCommands($value, $name);
				}
				elseif($key === self::KEY_RUNNABLE)
				{
					$action->runnable = (bool)$value;
				}
				else
				{
					$action->config->set($key, $value);
				}
			}
			
			if(!is_array($action->commands) || count($action->commands) === 0)
			{
				throw new Exception("Akce '$name' neobsahuje zadne prikazy.");
			}
			
			return $action;
		}
		
		
		
		/**
		 * @param	array
		 * @param	string
		 * @return	Heymaster\Command[]
		 * @todo	2012-11-25  pridat podporu pro NeonEntity
		 */
		protected function processCommands(array $array, $parentName)
		{
			$res = array();
			
			foreach($array as $key => $value)
			{
				$command = self::createCommand();
				
				if(is_int($key)) // simple command syntax
				{
					$command->name = $value;
				}
				elseif(is_array($value)) // complex command syntax
				{
					$extractedName = self::extractCommandName($key);
					$command->name = $extractedName['name'];
					$command->description = $extractedName['description'];
					$command->params = $value;
				}
				else
				{
					throw new Exception("Prikaz '$key' v akci '$parentName' je prazdny, nebo neni validni.");
				}
				
				$res[] = $command;
			}
			
			return $res;
		}
		
		
		
		/**
		 * @param	string
		 * @return	array	['name' => (string), 'description' => (FALSE|string)]
		 */
		public static function extractCommandName($str)
		{
			$res = array(
				'name' => $str,
				'description' => FALSE,
			);
			
			if(($pos = strpos($str, ' ')) !== FALSE)
			{
				$res['name'] = trim(substr($str, 0, $pos));
				$res['description'] = trim(substr($str, $pos + 1));
			}
			
			return $res;
		}
	}

