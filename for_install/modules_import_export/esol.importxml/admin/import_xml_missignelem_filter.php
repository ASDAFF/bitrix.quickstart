<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$arGet = $_GET;
$IBLOCK_ID = (int)$arGet['IBLOCK_ID'];
$PROFILE_ID = (int)$arGet['PROFILE_ID'];

if($_POST && isset($_POST['FILTER']))
{
	$arFilterKeys = preg_grep('/^filter1_/', array_keys($_POST));
	if(!empty($arFilterKeys))
	{
		if(!is_array($_POST['FILTER']))
		{
			$_POST['FILTER'] = array();
		}
		foreach($arFilterKeys as $key)
		{
			$arKey = explode('_', $key, 2);
			$_POST['FILTER'][$arKey[1]] = $_POST[$key];
		}
	}
	
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
	echo base64_encode(serialize($_POST['FILTER']));
	die();
}

if($OLDFILTER) $FILTER = unserialize(base64_decode($OLDFILTER));
if(!is_array($FILTER)) $FILTER = array();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="filter_form" id="esol-ix-filter" class="esol-ix-filter">
	<?\Bitrix\EsolImportxml\Utils::ShowFilter('esol_importxml_'.$PROFILE_ID, $IBLOCK_ID, $FILTER);?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>