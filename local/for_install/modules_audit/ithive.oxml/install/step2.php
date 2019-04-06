<?if(!check_bitrix_sessid()) return;?>
<?
global $errors;
require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
?>
<form action="<?= $APPLICATION->GetCurPage()?>" name="ithive_oxml_install" method="POST" class="ithive_oxml_install">
	<script>
		document.getElementById('adm-title').innerHTML = '<?=GetMessage('ITHIVE_ADMIN_TITLE');?>';
	</script>
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<input type="hidden" name="id" value="ithive.oxml">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="3" id="step">
	
	<table>
	<?	
		
		function path($id){
			$nav = CIBlockSection::GetNavChain(false, $id);
			while($arNav = $nav->GetNext())	$path .= " / ".$arNav["NAME"];
			return $path;
		}
				
		foreach($iblocks_to_export as $iblock) {
			$arFilter = Array("IBLOCK_ID" => $iblock);
			$count = CIBlockSection::GetCount($arFilter);
			if ($count < 1) {
			} else {
				$rsIBlockSection = CIBlockSection::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $iblock, "ACTIVE"=>"Y", "INCLUDE_SUBSECTIONS" => "Y"), true);	
				while($arr = $rsIBlockSection->Fetch())
				{
					$arIBlockSection[$arr["ID"]] .= '['.$arr['IBLOCK_CODE'].'] '.path($arr['ID']);
				}
			}
		}	
		natsort($arIBlockSection);
		
		?>
			<tr>
				<td><label for='sections_to_export[]'><?=GetMessage('ITHIVE_SECTIONS_TO_EXPORT')?></label></td>
				<td>
					<select name='sections_to_export[]' multiple size='8'>
						<option value='0' selected><?=GetMessage('ITHIVE_ALL_SECT')?></option>
					<?foreach($arIBlockSection as $key=>$value) {?>
						<option value='<?=$key?>'><?=$value?></option>
					<?}?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td width="200px"><?=GetMessage('ITHIVE_SECTIONS_TO_EXPORT_DESC')?></td>
			</tr>
		<?
		$rsIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $iblocks_to_export, "ACTIVE"=>"Y"));
		
		$arIBlock = CIBlock::GetList(Array("sort" => "asc"), Array("IBLOCK_ID" => $iblocks_to_export, "ACTIVE"=>"Y"));
		
		while($arr = $arIBlock->Fetch())
		{
			$dbProperty = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arr['ID']));
			while($arProperty = $dbProperty->Fetch()) {
				if ($arProperty['CODE'] == 'CML2_LINK') {
					$arSKUProps = 'PROPERTY_'.$arProperty['CODE'];
					continue;
				}
				if ($arProperty["PROPERTY_TYPE"] == "F") continue;
				
				$arProps[$arr['CODE'].'_'.$arProperty['CODE']] = "[{$arr['CODE']}] [{$arProperty['CODE']}] {$arProperty['NAME']}";
			}
			
			$dbMore = CIBlockProperty::GetList(array(), array("IBLOCK_ID" => $arr["ID"], "PROPERTY_TYPE" => "F"));
			while($arMore = $dbMore->Fetch()) $arProp[$arr['CODE'].'_'.$arMore['CODE']] = "[{$arr['CODE']}] [{$arMore['CODE']}] {$arMore['NAME']}";
		}
			
		natsort($arProps);
		?>
			<tr>
				<td><label for='properties_to_export[]'><?=GetMessage('ITHIVE_PROPS_TO_EXPORT')?></label></td>
				<td>
					<select name='properties_to_export[]' multiple size='8'>
						<option value='0' selected><?=GetMessage('ITHIVE_ALL_PROP')?></option>
					<?foreach($arProps as $key=>$value) {?>
						<option value='<?=$key?>'><?=$value?></option>
					<?}?>
					</select>
				</td>
				<td>&nbsp;</td>
				<td width="200px"><?=GetMessage('ITHIVE_PROPS_TO_EXPORT_DESC')?></td>
			</tr>
			<tr>
				<td><label for='more_photo'><?=GetMessage('ITHIVE_PHOTO_TO_EXPORT')?></label></td>
				<td>
					<select name='more_photo'>
					<?foreach($arProp as $key=>$value) {?>
						<option value='<?=$key?>' <?=(preg_match('|PHOTO|', $value))?'selected':''?>><?=$value?></option>
					<?}?>
					</select>
				</td>
			</tr>
			<!--tr>
				<td width="250px"><label for='sales_notes'><?=GetMessage('ITHIVE_SALESN_TO_EXPORT')?></label></td>
				<td>
					<select name='sales_notes'>
					<?foreach($arProps as $key=>$value) {?>
						<option value='<?=$key?>'><?=$value?></option>
					<?}?>
					</select>
				</td>
			</tr-->
			
			<input type='hidden' name='sku_properties_to_export' value='<?=$arSKUProps?>' id='sku_properties_to_export' />
			
	</table>
	<?
		$random = RandString(7);
		$arOptions = array(
			"site" => array(
				"dir_to_install" => $dir_to_install,
				"site_name" => $site_name,
				"company_name" => $company_name,
				"server_name" => $server_name,
				"dir_full" => "http://".$_SERVER['HTTP_HOST'].$dir_to_install."openboomapp-export-".$random.".php",
				"random_name" => "openboomapp-export-".$random.".php",
				"price_type" => $price_types
			), 
			"iblocks" => $iblocks_to_export,
		);
		
		COption::SetOptionString('ithive.oxml', 'options', serialize($arOptions));
		
		echo GetMessage('INSTALL_IN_DIR', array("#directory#" => "<b>".$arOptions["site"]["dir_full"]."</b>"));
		
	?>	
	<input type='hidden' name='dir_full' value='<?=$arOptions["site"]["dir_full"]?>' />
	
	<br/>
	
	<input type="submit" name="inst" value="<?echo GetMessage("INSTALL_MODULE")?>" class="adm-btn-save">	
	
	<style>
		.ithive_oxml_install {
			position: relative;
		}
		.ithive_oxml_install .adm-btn-save{
			position: absolute;
			left: 150px;
			top: 60px;
		}
		.prev-step {
			float:left;
			margin-top: 30px;
		}
	</style>
	
</form>

<form action="<?=$APPLICATION->GetCurPage()?>" method="POST">
	<?=bitrix_sessid_post()?>
	<input type="hidden" name="lang" value="<?= LANG ?>">
	<input type="hidden" name="id" value="ithive.oxml">
	<input type="hidden" name="install" value="Y">
	<input type="hidden" name="step" value="1" id="step">
	
	<input type="submit" name='inst' value="<?echo GetMessage("PREV_STEP")?>" class='prev-step'>	
</form>