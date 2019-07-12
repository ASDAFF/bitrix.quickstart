<?if(!defined("B_PROLOG_INCLUDED")||B_PROLOG_INCLUDED!==true)die();

if(isset($_REQUEST['link']) && trim($_REQUEST['link'])!='')
{
	foreach($arResult['FIELDS'] as $key => $arField)
	{
		if($arField['CONTROL_NAME']=='RS_LINK')
		{
			$arResult['FIELDS'][$key]['VALUE'] = $_REQUEST['link'];
			break;
		}
	}
}