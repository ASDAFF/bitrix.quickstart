<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
$frame = $this->createFrame()->begin('');

$arrIDs = array();
foreach($arResult as $arItem){
	$arrIDs[$arItem['ID']] = 'Y';
}
?><script>
	RSMONOPOLY_COMPARE = <?if(count($arrIDs)>0) { echo json_encode($arrIDs); } else { echo '{}'; }?>;
	RS_MONOPOLY_COUNT_COMPARE = <?=(count($arResult))?>;
	RSMONOPOLY_SetCompared();
</script><?