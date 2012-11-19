<?php
	/** Default Git Handler
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-11-19-1
	 */
	
	namespace Heymaster\Git;
	
	class Git extends \Nette\Object implements IGit
	{
		public function tag($name)
		{
			$this->run("git tag $name");
		}
		
		
		
		public function merge($brach)	// TODO: intoThis parameter
		{
			$this->run("git merge $brach");
		}
		
		
		
		public function branchCreate($name, $checkout = FALSE)
		{
			// git branch $name
			$this->run("git branch $name");
			$this->checkout($name);
		}
		
		
		
		public function branchRemove($name)
		{
			$this->run("git branch -d $name");
		}
		
		
		
		public function checkout($name)
		{
			$this->run("git checkout $name");
		}
		
		
		
		protected function run($cmd)
		{
			$success = system($cmd, $ret);
			
			if($success === FALSE || $ret !== 0)
			{
				throw new \Exception("Prikaz '$cmd' selhal.");
			}
		}
	}

