<?
IncludeModuleLangFile(__FILE__);

$classes = array(
	'WebprofyAutoreplace' => 'classes/general/webprofy_autoreplace.php'
);

foreach(array(
	'Autoreplace' => array(
		'View',
		'Ajax'
	),
	/*'Webprofy' => array(
		'Bitrix\Module',
		'Bitrix\Attribute\Attribute',
		'Bitrix\Attribute\Attributes',
		'Bitrix\Attribute\AttributesTree',
		'WP'
	)
	*/
) as $ns => $a){
	foreach($a as $name){
		$classes[$ns.'\\'.$name] = 'classes/general/'.$ns.'/'.strtr($name, array('\\' => '/')).'.php';
	}
	
}

CModule::AddAutoloadClasses('webprofy.autoreplace', $classes);

global $DBType;
?>