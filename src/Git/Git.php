<?php
	/** Default Git Handler
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-06-2
	 */
	
	namespace Heymaster\Git;
	
	use Heymaster\Git\GitException;
	
	class Git extends \Nette\Object implements IGit
	{
		public function tag($name)
		{
			$this->run("git tag $name");
			return $this;
		}
		
		
		
		public function merge($branch, $options = NULL)
		{
			$this->run("git merge", $options, $branch);
			return $this;
		}
		
		
		
		public function branchCreate($name, $checkout = FALSE)
		{
			// git branch $name
			$this->run("git branch $name");
			
			if($checkout)
			{
				$this->checkout($name);
			}
			
			return $this;
		}
		
		
		
		public function branchRemove($name)
		{
			$this->run("git branch -d $name");
			return $this;
		}
		
		
		
		public function branchName()
		{
			$output = array();
			$exitCode = NULL;
			
			exec('git branch', $output, $exitCode);
			
			if($exitCode === 0 && is_array($output))
			{
				foreach($output as $line)
				{
					if($line[0] === '*')
					{
						return trim(substr($line, 1));
					}
				}
			}
			
			throw new GitException('Pokus o ziskani jmena aktualni vetvi selhal.');
		}
		
		
		
		public function checkout($name)
		{
			$this->run("git checkout $name");
			return $this;
		}
		
		
		
		public function remove($file)
		{
			$this->run("git rm $file");
			return $this;
		}
		
		
		
		public function add($file)
		{
			$this->run("git add $file");
			return $this;
		}
		
		
		
		public function commit($message)
		{
			$this->run("git commit -m \"$message\"");
			return $this;
		}
		
		
		
		/**
		 * @param	string|array
		 * @return	void
		 * @throws	Heymaster\Git\GitException
		 */
		protected function run($cmd/*, $options = NULL*/)
		{
			$args = func_get_args();
			$cmd = array();
			
			foreach($args as $arg)
			{
				if(is_array($arg))
				{
					foreach($arg as $key => $value)
					{
						$cmd[] = "$key $value";
					}
				}
				elseif(is_scalar($arg) && !is_bool($arg))
				{
					$cmd[] = $arg;
				}
			}
			
			$cmd = implode(' ', $cmd);
			$success = system($cmd, $ret);
			
			if($success === FALSE || $ret !== 0)
			{
				throw new GitException("Prikaz '$cmd' selhal.");
			}
		}
	}

