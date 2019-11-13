<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>

<?$curDepth = $arResult["SECTIONS"][0]["DEPTH_LEVEL"]?>
<table cellpadding="0" cellspacing="0" border="0" width="100%" class="firstTable">

<?
global $sectionsList;
?>

<? 
$cols="5"; //Количество колонок 
$k="0"; 
?>
<table cellpadding="3" cellspacing="0" border="0" width="100%"><tr> 
<?foreach($arResult["SECTIONS"] as $arSection): if($arSection["DEPTH_LEVEL"]!=$curDepth){continue;} $k++;?>
<?$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));?>
    <?if($k>$cols){$k=1;?></tr><tr><?}?> 
    <td id="<?=$this->GetEditAreaId($arSection['ID']);?>" class="section" align="center" valign="top" width="<?=count(100/$cols)?>%"> 
		
		<?
$sectionsList[] = $arSection["ID"];
	$this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
	$this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
?>
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
<?$count++;?>
    </td>
<?endforeach;?>
 
<?if($k!=$cols){for($i=1; $i<$cols-$k; $i++){?> 
<td>&nbsp;</td> 
<?}}?>
</tr></table>