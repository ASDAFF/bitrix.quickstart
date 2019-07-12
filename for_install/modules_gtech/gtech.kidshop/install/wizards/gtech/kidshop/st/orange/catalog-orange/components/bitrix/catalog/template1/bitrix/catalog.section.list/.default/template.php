<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$curDepth = $arResult["SECTIONS"][0]["DEPTH_LEVEL"]?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="firstTable">

<?
global $sectionsList;
$count = 0;
foreach($arResult["SECTIONS"] as $arSection):
	if($arSection["DEPTH_LEVEL"]!=$curDepth){continue;}
$sectionsList[] = $arSection["ID"];
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
?>
<tr valign="center" id="trHeight<?=$count;?>" height="160px">
	<td id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="section_td" height="160px" valign="center">
	<div id="section_name">
		<table cellpadding="0" cellspacing="0" border="0" width="100%">
			<tr><td class="section_name_top"></td></tr>
			<tr><td class="section_name_bg">
				<a href="<?=$arSection["SECTION_PAGE_URL"]?>"><?=$arSection["NAME"]?></a>
			</td></tr>
			<tr><td class="section_name_bottom">&nbsp;</td></tr>
		</table>
	</div>
		<table cellpadding="0" cellspacing="0" border="0" width="100%" class="imgTable">
			<tr><td class="section_top">&nbsp;</td></tr>
			<tr><td class="section_bg" align="center" valign="middle">
					<?if($arSection["DETAIL_PICTURE"]){
						$arSecImg = CFile::ResizeImageGet($arSection[DETAIL_PICTURE],Array("width"=>"130","height"=>"130"));?>
						<img src="<?=$arSecImg[src]?>" border="0">
					<?}else{?><img src="<?=$templateFolder?>/images/section_nophoto.jpg" border="0"><?}?>
			</td></tr>
			<tr><td class="section_bottom">&nbsp;</td></tr>
		</table>
	</td>

</tr>
<?$count++;?>
<?endforeach?>

</table>