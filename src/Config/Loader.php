<?php
	/** Heymaster ILoader interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2013-02-27-1
	 */
	
	namespace Heymaster\Config;
	use Heymaster\InvalidException,
		Hwymaster\Adapters\IAdapter;
	
	class Loader implements ILoader
	{
		/** @var  Heymaster\Adapters\IAdapter */
		protected $adapter;
		
		
		
		public function __construct(IAdapter $adapter)
		{
			$this->adapter = $adapter;
		}
		
		
		
		/**
		 * @param	string
		 * @return	array
		 */
		public function load($file)
		{
			if(substr($file, -5) === '.neon')
			{
				return $adapter->load($file);
			}
			
			throw new InvalidException("Neznama pripona souboru: $file");
		}
	}

