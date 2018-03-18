<? if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die(); 

if(!$arParams['INCLUDE_OPENAPI']) $arParams['INCLUDE_OPENAPI'] = 'Y';

if($arParams['INCLUDE_OPENAPI'] == 'Y')
{
	$APPLICATION->AddHeadString('<script type="text/javascript" src="http://userapi.com/js/api/openapi.js?34"></script>', true);
}

$APPLICATION->AddHeadString('<script type="text/javascript">VK.init({apiId: '.$arParams['APP_ID'].', onlyWidgets: true});</script>', true);

$attach = array();
if($arParams['ALLOW_GRAFFITI'] == 'Y') $attach[] = 'graffiti';
if($arParams['ALLOW_PHOTOS'] == 'Y') $attach[] = 'photo';
if($arParams['ALLOW_VIDEOS'] == 'Y') $attach[] = 'video';
if($arParams['ALLOW_AUDIO'] == 'Y') $attach[] = 'audio';
if($arParams['ALLOW_LINKS'] == 'Y') $attach[] = 'link';

if(count($attach) == 0) $attach = 'false';
elseif(count($attach) == 5) $attach = '"*"';
else
{
	$attach = implode(',', $attach);	
	$attach = '"'.$attach.'"';
}

$arResult['CODE'] = '<div id="vk_comments"></div>
<script type="text/javascript">VK.Widgets.Comments("vk_comments", {limit: '.intval($arParams['COUNT']).', width: "'.intval($arParams['WIDTH']).'", attach: '.$attach.'});</script>';
$this->IncludeComponentTemplate();
?>