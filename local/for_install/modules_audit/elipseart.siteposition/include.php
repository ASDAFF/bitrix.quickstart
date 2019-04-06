<?
global $DB, $APPLICATION, $MESS, $DBType; 

CModule::AddAutoloadClasses(
	"elipseart.siteposition",
	array(
		'CEASitePositionHost' => 'classes/'.$DBType.'/host.php',
	 	'CEASitePositionKeyword' => 'classes/'.$DBType.'/keyword.php',
	 	'CEASitePosition' => 'classes/'.$DBType.'/position.php',
	 	'CEASitePositionBing' => 'classes/'.$DBType.'/position_bing.php',
	 	'CEASitePositionGoogle' => 'classes/'.$DBType.'/position_google.php',
		'CEASitePositionUpdate' => 'classes/'.$DBType.'/position_update.php',
		'CEASitePositionYandex' => 'classes/'.$DBType.'/position_yandex.php',
		'CEASitePositionRegion' => 'classes/'.$DBType.'/region.php',
		'CEASitePositionSearchSystem' => 'classes/'.$DBType.'/search_system.php',
	)
);
?>