<?

CModule::AddAutoloadClasses(
	'aspro.optimus',
	array(
		'COptimusCache' => 'classes/general/COptimusCache.php',
		'COptimus' => 'classes/general/COptimus.php',
		'COptimusTools' => 'classes/general/COptimusTools.php',
	)
);

// include common aspro functions
//include_once __DIR__ .'/classes/general/COptimusCache.php';
?>