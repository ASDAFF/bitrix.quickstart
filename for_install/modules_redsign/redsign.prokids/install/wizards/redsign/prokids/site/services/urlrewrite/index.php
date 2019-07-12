<?if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED!==true) die();
	
$arUrlRewrite = array(); 
if(file_exists(WIZARD_SITE_ROOT_PATH.'/urlrewrite.php')){
	include(WIZARD_SITE_ROOT_PATH.'/urlrewrite.php');
}

$arUrlsRewritesNew = array(
	array(
		'CONDITION' => '#^#SITE_DIR#personal/delivery/#',
		'RULE' => '',
		'ID' => 'bitrix:sale.personal.profile',
		'PATH' => '#SITE_DIR#personal/delivery/index.php',
	),
	array(
		'CONDITION' => '#^#SITE_DIR#catalog/#',
		'RULE' => '',
		'ID' => 'bitrix:catalog',
		'PATH' => '#SITE_DIR#catalog/index.php',
	),
	array(
		'CONDITION' => '#^#SITE_DIR#action/#',
		'RULE' => '',
		'ID' => 'bitrix:news',
		'PATH' => '#SITE_DIR#action/index.php',
	),
	array(
		'CONDITION' => '#^#SITE_DIR#brands/#',
		'RULE' => '',
		'ID' => 'bitrix:news',
		'PATH' => '#SITE_DIR#brands/index.php',
	),
	array(
		'CONDITION' => '#^#SITE_DIR#news/#',
		'RULE' => '',
		'ID' => 'bitrix:news',
		'PATH' => '#SITE_DIR#news/index.php',
	),
);

// dont lose old rewrite rules
$result = array_merge($arUrlRewrite, $arUrlsRewritesNew);
	
foreach($result as $key => $item){
	CUrlRewriter::Add($item);
}