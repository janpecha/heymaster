<?php
	/** Heymaster Adapter Interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-27-1
	 */
	
	namespace Heymaster\Adapters;
	
	use Heymaster\Adapters\Exception,
		Nette\Utils\Neon;
	
	class NeonAdapter extends BaseAdapter
	{
		/**
		 * @param	string
		 * @return	array|FALSE
		 */
		public function load($filename)
		{
			parent::load($filename);
			$res = $this->fromFile($filename);
			
			if(is_array($res))
			{
				$this->process($res);
				return $this->configuration;
			}
			
			return FALSE;
		}
		
		
		
		/**
		 * @param	string
		 * @return	mixed
		 */
		protected function fromFile($filename)
		{
			return Neon::decode(@file_get_contents($filename)); /* @ - soubor nemusi existovat
				v tu chvili vyhodi Neon vyjimku protoze mu bude z funkce file_get_contents
				predana hodnota FALSE.
			*/
		}
		
		
		
		/**
		 * @param	array
		 * @return	array|FALSE
		 */
		protected function process(array $array)
		{
			/*
				root: (value)
				output: (value)
				inherit: (value)
				message: (value)
				
				parameters: (array)
				
				before: (array|NULL)
				after: (array|NULL)
			*/
			foreach($array as $key => $value)
			{
				switch($key)
				{
					case self::SECTION_BEFORE:
					case self::SECTION_AFTER:
						$res = $this->processSection($key, $value);
						
						if($res === NULL) // empty section
						{
							// remove section
							$this->configuration[self::KEY_SECTIONS][$key] = NULL;
						}
						continue;
					
					case self::KEY_PARAMETERS:
						$this->processParameters($value);
						continue;
					
					case self::KEY_ROOT:
					case self::KEY_INHERIT:
					case self::KEY_OUTPUT:
					case self::KEY_MESSAGE:
						$this->configuration[$key] = $value;
						continue;
					
					default:
						throw new AdapterException("Neznama volba '$key'.");
				}
			}
		}
		
		
		
		/** Zpracuje $value (sekci 'before' (resp. 'after') z configu) a vysledek
		 *  ulozi do odpovidajici polozky v $configuration
		 * 
		 * @param	string  section name
		 * @param	array
		 * @return	TRUE|NULL
		 */
		protected function processSection($key, $value)
		{
			/*
				<section>: NULL #empty section
				
				## For example
				before: #nothing
				after: NULL
			*/
			if($value === NULL)
			{
				return;
			}
			
			/*
				<section>: 'Hello!'
				<section>: 10
				<section>: entity(params)
			*/
			if(!is_array($value))
			{
				throw new AdapterException("Neplatny obsah sekce '$key'");
			}
			
			/*
				<section>:
					root: (value)
					message: (value)
					output: (value)
					
					<action name 1>: (array|NULL)
					<action name 2>: (array|NULL)
					...
			*/
			foreach($value as $skey => $value)
			{
				switch($skey)
				{
					case self::KEY_ROOT:
					case self::KEY_MESSAGE:
					case self::KEY_OUTPUT:
						$this->configuration[self::KEY_SECTIONS][$key][$skey] = $value;
						continue;
					
					default:
						$this->processAction($key, $skey, $value); /* sectionName, actionName, actionContent */
				}
			}
			
			return TRUE;
		}
		
		
		
		protected function processAction($sectionName, $actionName, $value)
		{
			/*
				<action name>: NULL
				<action name>: #nothing
			*/
			if($value === NULL) // empty value (NULL) is ignored
			{	
				$this->addWarning("Akce '$actionName' v sekci '{$sectionName}' je prazdna.");
				return;
			}
			
			/*
				<action name>: 'Hello!'
				<action name>: 10
				<action name>: entity(params)
			*/
			if(!is_array($value))
			{
				throw new AdapterException("Akce '$actionName' v sekci '{$sectionName}' neni validni.");
			}
			
			if(isset($this->configuration[self::KEY_SECTIONS][$sectionName][self::KEY_ACTIONS][$actionName]))
			{
				throw new AdapterException("Zdvojeny klic - akce '$actionName' je v sekci '$sectionName' uvedena dvakrat.");
			}
			
			$configuration = &$this->configuration[self::KEY_SECTIONS][$sectionName][self::KEY_ACTIONS][$actionName];
			$configuration = self::createAction();
			
			/*
				<action name>:
					root: (value)
					output: (value)
					message: (value)
					runnable: (value)
					mask: (value)
					
					TODO: zachovat klic actions? Nyni BC-BREAK!!!
					<command name>: (array|NULL) # command with params
					- <command name> # command without params
			*/
			foreach($value as $key => $command)
			{
				switch((string)$key)
				{
					case self::KEY_ROOT:
					case self::KEY_OUTPUT:
					case self::KEY_MESSAGE:
					case self::KEY_RUNNABLE:
					case self::KEY_MASK:
						$configuration[$key] = $command;
						continue;
					
					default:
						$configuration[self::KEY_COMMANDS][] = $this->processCommand($sectionName, $actionName, $key, $command);
				}
			}
			
			if(count($configuration[self::KEY_COMMANDS]) === 0)
			{
				$this->addWarning("Akce '$actionName' neobsahuje zadne prikazy.");
			}
		}
		
		
		
		protected function processCommand($sectionName, $actionName, $commandName, $value)
		{
			$command = self::createCommand();
			
			if(is_int($commandName)) // simple command syntax (command without params)
			{
				$command[self::KEY_NAME] = $value;
			}
			elseif(is_array($value) || $value === NULL)
			{
				$extractedName = self::extractCommandName($commandName);
				$command[self::KEY_NAME] = $extractedName['name'];
				$command[self::KEY_DESCRIPTION] = $extractedName['description'];
				$command[self::KEY_PARAMS] = $value;
			}
			else // TODO: add support for instance of NeonEntity
			{
				throw new AdapterException("Prikaz '$commandName' v akci '$actionName' v sekci '$sectionName' neni validni.");
			}
			
			return $command;
		}
		
		
		
		/**
		 * @param	array
		 */
		protected function processParameters(array $parameters = NULL)
		{
			if($parameters === NULL)
			{
				return;
			}
			
			foreach($parameters as $key => $value)
			{
				$this->addParameter($key, $value);
			}
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

