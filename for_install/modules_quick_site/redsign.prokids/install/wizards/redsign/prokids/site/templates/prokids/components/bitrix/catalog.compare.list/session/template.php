<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$frame = $this->createFrame()->begin('');

$arrIDs = array();
foreach($arResult as $arItem)
{
	$arrIDs[$arItem['ID']] = 'Y';
}
?><script>RSGoPro_COMPARE = <?if(count($arrIDs)>0) { echo json_encode($arrIDs); } else { echo '{}'; }?>;RSGoPro_SetCompared();</script><?