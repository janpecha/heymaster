<?php
	/** Default Logger
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster\Logger;
	use Heymaster\Cli\Cli,
		Nette;
	
	class DefaultLogger extends Nette\Object implements ILogger
	{
		/** @var  string[] */
		private $prefixes = array();
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function log($str)
		{
			Cli::log($this->getPrefix() . $str);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function error($str)
		{
			Cli::error($this->getPrefix() . $str);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function success($str)
		{
			Cli::success($this->getPrefix() . $str);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function warn($str)
		{
			Cli::warn($this->getPrefix() . $str);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function info($str)
		{
			Cli::info($this->getPrefix() . $str);
			return $this;
		}
		
		
		
		/**
		 * @param	string
		 * @return	$this
		 */
		public function prefix($prefix)
		{
			$this->prefixes[] = ($prefix !== FALSE ? (string)$prefix : $prefix);
			return $this;
		}
		
		
		
		/**
		 * @return	$this
		 */
		public function end()
		{
			array_pop($this->prefixes);
			return $this;
		}
		
		
		
		/**
		 * @return	string
		 */
		protected function getPrefix()
		{
			$prefix = end($this->prefixes);
			return $prefix !== FALSE ? "[$prefix] " : '';
		}
	}

