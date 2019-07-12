<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

$arPhoto = array();

foreach($_REQUEST['arWidth'] as $key => $val)
{
	$buffer = Novagroup_Classes_General_Main::MakeResizeImagick(
		//$key,
		//array("WIDTH" => $val, "HEIGHT" => $_REQUEST['arHeight'][ $key ])
		array(
			'IMG_SRC'	=> htmlspecialchars($_REQUEST['arSource'][$key]),
			'IMG_ID'	=> (int)$key,
			'WIDTH'		=> (int)$val,
			'HEIGHT'	=> (int)$_REQUEST['arHeight'][ $key ]
		)
	);
	if($buffer)
		$arPhoto[ $key ] = $buffer['src'];
	else $arPhoto[ $key ] = SITE_TEMPLATE_PATH."/images/nophoto.png";
	
	global $CACHE_MANAGER;
	$CACHE_MANAGER -> ClearByTag("catalog.list.".(int)$_REQUEST['arElmId'][ $key ]);
}
//BXClearCache(true, "/s1/bitrix/catalog.element.preview/");
echo json_encode($arPhoto);
?>