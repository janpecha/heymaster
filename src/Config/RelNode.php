<?php
	/** Heymaster RelNode Class
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Config;
	use Nette;
	
	class RelNode extends Nette\Object
	{
		/** @var  string */
		public $dir;
		
		/** @var  array */
		public $children = array();
		
		/** @var  mixed|NULL */
		public $scope = NULL;
		
		
		
		/**
		 * @param	string|string[]
		 * @param	mixed|NULL
		 * @return	bool
		 */
		public function addChild($dir, $scope)
		{
			if(!is_array($dir))
			{
				$dir = explode('/', trim($dir, '/'));
			}
			
			if(count($dir) === 0 || $dir === FALSE)
			{
				$this->scope = $scope;
				return TRUE;
			}
			
			if($this->dir !== ''/*FS root*/)
			{
				if($this->dir !== reset($dir))
				{
					return FALSE; // TODO: throw exception??
				}
				
				array_shift($dir); // $this->dir === $dir[0]
			}
			
			$childDir = reset($dir);
			
			if($childDir === FALSE)
			{
				$this->scope = $scope;
				return TRUE;
			}
			
			if(!isset($this->children[$childDir]))
			{
				$this->children[$childDir] = self::create($childDir, NULL);
			}
			
			$this->children[$childDir]->addChild($dir, $scope);
			return TRUE;
		}
		
		
		
		/**
		 * @return	RelNode
		 */
		public function getFirstFilled()
		{
			if($this->scope !== NULL)
			{
				return $this;
			}
			
			foreach($this->children as $child)
			{
				$res = $child->getFirstFilled();
				
				if($res !== NULL || $res !== FALSE)
				{
					return $res;
				}
			}
			
			return FALSE;
		}
		
		
		
		/**
		 * @return	array
		 */
		public function getNearestChildren()
		{
			$nearest = array();
			
			foreach($this->children as $childNode)
			{
				if($childNode->scope !== NULL)
				{
					$nearest[] = $childNode;
				}
				else
				{
					$nearest = array_merge($nearest, $childNode->getNearestChildren());
				}
			}
			
			return $nearest;
		}
		
		
		
		/**
		 * @param	string
		 * @param	mixed|NULL
		 * @return	RelNode
		 */
		public static function create($dir, $scope)
		{
			$node = new static;
			$node->dir = $dir;
			$node->scope = $scope;
			
			return $node;
		}
	}

