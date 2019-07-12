<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if(!empty($arResult['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE'])){
	$arResult['DISPLAY_PROPERTIES']['PRICE']['VALUE'] = preg_replace('/\s/', '', $arResult['DISPLAY_PROPERTIES']['PRICE']['VALUE']);
	$arResult['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE'] =
		number_format($arResult['DISPLAY_PROPERTIES']['PRICE']['VALUE'], 2, ',', ' ').$arParams["CURRENCY_CODE"];
}
?>
<table border="0" cellspacing="0" summary="" cellpadding="0" width="100%">       
<tr valign="top">
	<td width="400">  		
		<?if(is_array($arResult["PREVIEW_PICTURE"])):?>
			<a target="_blank" href="<?=$arResult["DETAIL_PICTURE"]["SRC"]?>" rel="lightbox[main]">
			<?= CFile::ShowImage($arResult["PREVIEW_PICTURE"]["ID"], 400, 400, 'alt="'.$arResult["PREVIEW_PICTURE"]["DESCRIPTION"].'" border=0') ?></a>			
		<?endif?>
		<?php /*<p align="center"><img src="/i/rass.jpg" border="0"></p>*/?>

	</td>            
	<td>&nbsp;&nbsp;</td>            
	<td style="FONT-FAMILY: Tahoma; " width="100%">    

		<h3><?=$arResult["NAME"]?></h3>				
		<table border="0" cellpadding="0" cellspacing="0" width="238">
		    <tbody>
				<tr>
					<td width="135" height="45" bgcolor="#00608A">
						<p align="center"><b><font size="1" face="Verdana" color="white"></font>
							<font size="4" face="Verdana" color="white"><?=$arResult['DISPLAY_PROPERTIES']['PRICE']['DISPLAY_VALUE']?></font></b></p>
					</td>
					<td width="30" height="45"><font color="white"><img 
						src="<?=SITE_TEMPLATE_PATH?>/components/bitrix/photo/catalog/images/price_u.gif" 
						width="30" height="45" border="0"></font></td> 
					<td width="110" height="45" valign="top"><?php /*<a href="/discount_coupon/">Хочу скидку!</a>*/?></td>
				</tr>
				<tr>
					<td colspan="2" align="left" height="40" valign="bottom">
						<a href="/company/oplata.php/"><img src="<?=SITE_TEMPLATE_PATH?>/components/bitrix/photo/catalog/bitrix/photo.detail/main/images/visa.gif" /></a>
					</td>
				</tr>
			</tbody>
		</table>		
		<p> 
			<?php 
//---------- Галерея
$result='';
foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY"]["VALUE"] as $g_sect_id){
	$arFilter = Array("IBLOCK_ID"=>$arResult["DISPLAY_PROPERTIES"]["GALLERY"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
						"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
	$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
	while($gallery_element = $res->GetNext()){
		$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
		$result.= '<a target="_blank" href="'.$src.'" rel="lightbox[main]" style="float: left; padding: 7px;">'.
					CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
	} 
}
if(!empty($result)){
	print '<div style="clear: both;"></div>'.$result;
}
//---------- Варианты трансформации
$result='';
foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY_TRANSFORM"]["VALUE"] as $g_sect_id){
	$arFilter = Array("IBLOCK_ID"=>$arResult["DISPLAY_PROPERTIES"]["GALLERY_TRANSFORM"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
						"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
	$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
	while($gallery_element = $res->GetNext()){
		$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
		$result.= '<a target="_blank" href="'.$src.'" rel="lightbox[TRANSFORM]" style="float: left; padding: 7px;">'.
					CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
	} 
}
if(!empty($result)){
	print '<div style="clear: both;"></div><div style="font-size: 8pt; padding-top: 8px;">'.
		$arResult["DISPLAY_PROPERTIES"]["GALLERY_TRANSFORM"]["NAME"].':</div>'.$result;
}
//---------- Варианты обивки
$result='';
foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY_UPHOLSTERY"]["VALUE"] as $g_sect_id){
	$arFilter = Array("IBLOCK_ID"=>$arResult["DISPLAY_PROPERTIES"]["GALLERY_UPHOLSTERY"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
						"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
	$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
	while($gallery_element = $res->GetNext()){
		$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
		$result.= '<a target="_blank" href="'.$src.'" rel="lightbox[UPHOLSTERY]" style="float: left; padding: 7px;">'.
					CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
	} 
}
if(!empty($result)){
	print '<div style="clear: both;"></div><div style="font-size: 8pt; padding-top: 8px;">'.
		$arResult["DISPLAY_PROPERTIES"]["GALLERY_UPHOLSTERY"]["NAME"].':</div>'.$result;
}
//----------
print '<div style="clear: both;"></div>';
			?>			
		</p>				
	</td>
</tr>
</table>
<table>
<tr>
<td width="70%">		<hr color="#cccccc" /> 
			<?if($arResult["DETAIL_TEXT"] || $arResult["PREVIEW_TEXT"]):?>		
				<?if($arResult["DETAIL_TEXT"]):?>
					<?=$arResult["DETAIL_TEXT"]?>
				<?elseif($arResult["PREVIEW_TEXT"]):?>
					<?=$arResult["PREVIEW_TEXT"]?>
				<?endif;?>			
			<?endif?><hr color="#cccccc" /> 
		</td></tr></table>
<div style='clear: both;'></div>
<?php 
//====================
$result='';
foreach($arResult["DISPLAY_PROPERTIES"]["ASSOC"]["VALUE"] as $id){
	$element = GetIBlockElement($id);
    $src = CFile::GetPath($element["DETAIL_PICTURE"]);				
	$PREVIEW_PICTURE = CFile::GetByID($element["PREVIEW_PICTURE"])->GetNext();
//	$DETAIL_PICTURE = CFile::GetByID($element["DETAIL_PICTURE"])->GetNext();
	$DETAIL_PICTURE["SRC"] = CFile::GetPath($element["DETAIL_PICTURE"]);
	$result.='<hr style="margin: 20px 0px; clear: both;">
	<table border="0" cellspacing="0" summary="" cellpadding="0" width="100%">       
	<tr valign="top">
		<td width="400">
			<a target="_blank" href="'.$DETAIL_PICTURE["SRC"].'" rel="lightbox[main'.$id.']">'.
				CFile::ShowImage($element["PREVIEW_PICTURE"], 400, 400, 'alt="'.$PREVIEW_PICTURE["DESCRIPTION"].'" border=0').'</a>
		</td>            
		<td width="10">&nbsp;&nbsp;&nbsp;</td>            
		<td style="FONT-FAMILY: Tahoma; " width="100%">            
			<h3>'.$element["NAME"].'</h3>         

			<p style="FONT-FAMILY: Tahoma; FONT-SIZE: 9pt" > ';
				if($element["DETAIL_TEXT"] || $element["PREVIEW_TEXT"]){	
					if($element["DETAIL_TEXT"]){
						$result.=$element["DETAIL_TEXT"];
					}elseif($element["PREVIEW_TEXT"]){
						$result.=$element["PREVIEW_TEXT"];
					}		
				}
			$result.='</p>		
		</td>
	</tr>
	</table>';
	//----------
	$result_='';	
	foreach($element["PROPERTIES"]["GALLERY"]["VALUE"] as $g_sect_id){
		$arFilter = Array("IBLOCK_ID"=>$element["PROPERTIES"]["GALLERY"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
							"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
		$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
		while($gallery_element = $res->GetNext()){
			$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
			$result_.= '<a target="_blank" href="'.$src.'" rel="lightbox[main'.$id.']" style="float: left; padding: 7px;">'.
						CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
		} 
	}
	if(!empty($result_)){
		$result.= '<div style="clear: both;"></div>'.$result_;
	}
	//----------
	$result_='';
	foreach($element["PROPERTIES"]["GALLERY_TRANSFORM"]["VALUE"] as $g_sect_id){
		$arFilter = Array("IBLOCK_ID"=>$element["PROPERTIES"]["GALLERY_TRANSFORM"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
							"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
		$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
		while($gallery_element = $res->GetNext()){
			$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
			$result_.= '<a target="_blank" href="'.$src.'" rel="lightbox[TRANSFORM'.$id.']" style="float: left; padding: 7px;">'.
						CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
		} 
	}
	if(!empty($result_)){
		$result.= '<div style="clear: both;"></div><div style="font-size: 8pt; padding-top: 8px;">'.
			$element["PROPERTIES"]["GALLERY_TRANSFORM"]["NAME"].':</div>'.$result_;
	}
	//----------
	$result_='';
	foreach($element["PROPERTIES"]["GALLERY_UPHOLSTERY"]["VALUE"] as $g_sect_id){
		$arFilter = Array("IBLOCK_ID"=>$element["PROPERTIES"]["GALLERY_UPHOLSTERY"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
							"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
		$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
		while($gallery_element = $res->GetNext()){
			$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
			$result_.= '<a target="_blank" href="'.$src.'" rel="lightbox[UPHOLSTERY'.$id.']" style="float: left; padding: 7px;">'.
						CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 100, 75, "border=0").'</a>';
		} 
	}
	if(!empty($result_)){
		$result.= '<div style="clear: both;"></div><div style="font-size: 8pt; padding-top: 8px;">'.
			$element["PROPERTIES"]["GALLERY_UPHOLSTERY"]["NAME"].':</div>'.$result_;
	}
	//----------
	$result.= '<div style="clear: both;"></div>';
}
if(!empty($result)){ 
	print $result.'<hr style="margin: 20px 0px; clear: both;">';
}
//====================
$result='';
foreach($arResult["DISPLAY_PROPERTIES"]["GALLERY_SCHEME"]["VALUE"] as $g_sect_id){
	$arFilter = Array("IBLOCK_ID"=>$arResult["DISPLAY_PROPERTIES"]["GALLERY_SCHEME"]["LINK_IBLOCK_ID"], "ACTIVE"=>"Y", "IBLOCK_ACTIVE"=>"Y",
						"SECTION_ID"=>$g_sect_id, "SECTION_ACTIVE"=> "Y");	
	$res = CIBlockElement::GetList(Array("sort"=>"asc","created"=>"desc"), $arFilter);
	while($gallery_element = $res->GetNext()){
		$src = CFile::GetPath($gallery_element["DETAIL_PICTURE"]);
		$gallery_element["NAME"]=trim($gallery_element["NAME"]);    
		if(!empty($gallery_element["NAME"])){    			
			$result.= '<div style="font-size: 8pt; padding-top: 10px; font-weight: 900;">'.$gallery_element["NAME"].':</div>';
		}
		$result.= '<div><a target="_blank" href="'.$src.'" rel="lightbox[SCHEME]" style=" padding: 7px;">';
		$result.= CFile::ShowImage($gallery_element["PREVIEW_PICTURE"], 650, 650, "border=0");
		$result.= '</a></div>';
	} 
}
if(!empty($arResult["DISPLAY_PROPERTIES"]['IMG_4DESIGNERS']) && !empty($arResult["DISPLAY_PROPERTIES"]['IMG_4DESIGNERS']['DISPLAY_VALUE'])){
	$result.=$arResult["DISPLAY_PROPERTIES"]['IMG_4DESIGNERS']['NAME'].':&nbsp;'.
								$arResult["DISPLAY_PROPERTIES"]['IMG_4DESIGNERS']['DISPLAY_VALUE'];
}
if(!empty($result)){ print $result; }
?>