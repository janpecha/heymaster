<?php
	/** Heymaster Loader
	 * 
	 * @author		Jan Pecha, <janpecha@email.cz>
	 * @version		2012-12-13-1
	 */
	
	require __DIR__ . '/src/exceptions.php';
	require __DIR__ . '/src/Heymaster.php';
	
	require __DIR__ . '/src/Config.php';
	require __DIR__ . '/src/Section.php';
	require __DIR__ . '/src/Action.php';
	require __DIR__ . '/src/Command.php';
	
	require __DIR__ . '/src/Configs/FileConfig.php';
	
	require __DIR__ . '/src/Adapters/IAdapter.php';
	require __DIR__ . '/src/Adapters/BaseAdapter.php';
	require __DIR__ . '/src/Adapters/NeonAdapter.php';
	
	require __DIR__ . '/src/Cli/Cli.php';
	require __DIR__ . '/src/Cli/IRunner.php';
	require __DIR__ . '/src/Cli/Runner.php';
	require __DIR__ . '/src/Logger/ILogger.php';
	require __DIR__ . '/src/Logger/DefaultLogger.php';
	
	require __DIR__ . '/src/Git/IGit.php';
	require __DIR__ . '/src/Git/Git.php';
	
	require __DIR__ . '/src/Utils/Finder.php';
	
	require __DIR__ . '/src/Commands/CommandSet.php';
	require __DIR__ . '/src/Commands/CoreCommands.php';
	require __DIR__ . '/src/Commands/PhpCommands.php';
#	require __DIR__ . '/src/Commands/JsCommands.php';
#	require __DIR__ . '/src/Commands/CssCommands.php';

