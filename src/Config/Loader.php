<?php
	/** Heymaster ILoader interface
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Config;
	use Heymaster\InvalidException,
		Heymaster\Adapters\IAdapter;
	
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
				return $this->adapter->load($file);
			}
			
			throw new InvalidException("Neznama pripona souboru: $file");
		}
	}

