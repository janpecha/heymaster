<?php
	/** Dependency Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-01-30-1
	 */
	
	namespace Jp;
	
	class Dependency
	{
		private $items = array();
		
		
		
		/**
		 * @param	string
		 * @param	string[]
		 * @return	$this
		 */
		public function add($item, array $deps = array())
		{
			$this->items[(string)$item] = $deps;
			
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	bool
		 */
		protected function checkDependencies($item)
		{
			if(!isset($this->items[$item]))
			{
				return FALSE;
			}

			foreach($this->items[$item] as $depend)
			{
				if(!$this->checkDependencies($depend))
				{
					return FALSE;
				}
			}

			return TRUE;
		}
		
		
		
		/**
		 * @return	string[]
		 */
		public function getResolved()
		{
			$result = array();
			
			foreach($this->items as $item => $depends)
			{
				if($this->checkDependencies($item))
				{
					$result[] = $item;
				}
			}
			
			return $result;
		}
		
		
		
		/**
		 * @return	string[]
		 */
		public function getUnresolved()
		{
			$result = array();
			
			foreach($this->items as $item => $depends)
			{
				if(!$this->checkDependencies($item))
				{
					$result[] = $item;
				}
			}
			
			return $result;
		}
	}

