<?php
	/** Heymaster
	 *
	 * @author		Jan Pecha, <janpecha@email.cz>
	 */
	
	namespace Heymaster;
	
	use Heymaster\Utils\Finder,
		Heymaster\Logger\ILogger,
		Heymaster\Config\Configurator,
		Heymaster\InvalidException;
	
	class Heymaster extends \Nette\Object
	{
		/** @var  Heymaster\Logger\ILogger */
		protected $logger;
		
		
		
		/**
		 * @param	Heymaster\Logger\ILogger
		 * @param	Heymaster\Cli\IRunner
		 * @param	string
		 */
		public function __construct(ILogger $logger)
		{
			$this->logger = $logger;
		}
		
		
		
		/**
		 * @param	Heymaster\Config\Configurator
		 * @param	Heymaster\Builders\IBuilder
		 * @return	void
		 */
		public function build(Configurator $configurator)
		{
			$this->logger->prefix('HM');
			
			try
			{
				// ziskani builderu
				$builder = $configurator->getBuilder();
				
				if(!$builder)
				{
					throw new InvalidException('HM: Builder neni nastaven. Nelze pokracovat.');
				}
				
				
				// nacteni parametru
				$parameters = $configurator->getParameters();
				$root = $parameters['root'];
				$testingMode = $parameters['testingMode'];
				$builderId = $parameters['builder'];
				$tag = $parameters['tag'];
				
				
				// kontrola zda jsme v rootu nejakeho projektu
				if(!file_exists("$root/heymaster.neon"))
				{
					throw new NotFoundException('Root file \'heymaster.neon\' not found.');
				}
				
				
				// Nastaveni builderu
				$builder->setTestingMode($testingMode);
				
				
				// Hledam konfiguracni soubory
				$this->logger->info("Hledam konfiguracni soubory v $root");
				
				foreach(Finder::findFiles('heymaster.neon')->from($root) as $file)
				{
					$configurator->addConfig($file);
					$this->logger->success($file);
				}
				
				
				// Zpracovavam nalezene soubory
				$this->logger->info('Zpracovavam nalezene soubory');
				$scope = $configurator->buildScopes(); // returns main scope
				
				// Predani parametru do builderu
				$builder->setParameters($scope->getParameter($builderId, array()));
				
				
				// Vytvarim sestaveni
				$this->logger->info('Vytvarim sestaveni');
				
				$builder->startup($tag);
				$scope->setWorkingRoot($builder->getWorkingRoot());
				
				$builder->preprocess();
				$scope->processBefore();
				$builder->postprocess();
				
				if(!$testingMode)
				{
					$scope->processAfter();
				}
				
				$builder->finish();				
				
				// Hotovo
				$this->logger->success('Done.');
			}
			catch(\Exception $e)
			{
				$this->logger->error('Doslo k chybe! Nelze pokracovat.')
					->error($e->getMessage())
					->error('File: ' . $e->getFile())
					->error('Line: ' . $e->getLine());
				
				foreach($e->getTrace() as $entry)
				{
					$file = (isset($entry['file']) && $entry['file'] !== '') ? $entry['file'] : 'unknow file';
					$line = (isset($entry['line'])) ? (' (' . $entry['line'] . ')') : '';
					$this->logger->error("File: $file $line");
				}
			}
			
			$this->logger->end();
		}
	}

