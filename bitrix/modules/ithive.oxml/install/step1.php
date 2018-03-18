<?if(!IsModuleInstalled("iblock"))
{
	echo CAdminMessage::ShowMessage(GetMessage("ITHIVE_OXML_INSTALL_IBLOCK"));
	?>
	<form action="<?echo $APPLICATION->GetCurPage()?>">
	<p>
		<input type="hidden" name="lang" value="<?echo LANG?>">
		<input type="submit" name="" value="<?echo GetMessage("MOD_BACK")?>">	
	</p>
	<form>
	<?
}
else
{
	require($_SERVER['DOCUMENT_ROOT']."/bitrix/modules/main/include/mainpage.php");
	?>
	<form action="<?= $APPLICATION->GetCurPage()?>" name="ithive_oxml_install" method="POST">
	<script>
		document.getElementById('adm-title').innerHTML = '<?=GetMessage('ITHIVE_ADMIN_TITLE');?>';
	</script>
	<?=bitrix_sessid_post()?>
		<input type="hidden" name="lang" value="<?= LANG ?>">
		<input type="hidden" name="id" value="ithive.oxml">
		<input type="hidden" name="install" value="Y">
		<input type="hidden" name="step" value="2" id="step">
				
		<?
			$options = unserialize(COption::GetOptionString('ithive.oxml', 'options'));
		
			$siteList = array();
			$rsSites = CSite::GetList($by="sort", $order="asc", Array());
			while($arRes = $rsSites->GetNext())
			{
				$siteList[$arRes['CULTURE_ID']] = $arRes;
			}			
			natsort($siteList);
		?>
		<table>
			<tr>
				<td><label for='dir_to_install'><?=GetMessage('NOT_IN_DEFAULT')?></label></td>
				<td>
					<select name='dir_to_install'>
					<?foreach($siteList as $sL) {?>
						<option value='<?=$sL['DIR']?>'><?='['.$sL['ID'].'] '.$sL['NAME']?></option>
					<?}?>
					</select>
				</td>
			</tr>
			
		<?
			$dbResult = CCatalog::GetList(
				Array("SORT"=>"ASC"),
				Array(),
				false
			);
			
			$dbPriceType = CCatalogGroup::GetList(
				array("SORT" => "ASC")
			);
		?>
			
			<!--tr>
				<td><label for='site_name'><?=GetMessage('ITHIVE_INSTALL_SITE_NAME')?></label></td>
				<td><input type='text' value='<?=($options['site']['site_name'])?$options['site']['site_name']:$siteList[1]["NAME"]?>' id='site_name' name='site_name' /></td>
			</tr>
			<tr>
				<td><label for='company_name'><?=GetMessage('ITHIVE_INSTALL_COMPANY_NAME')?></label></td>
				<td><input type='text' value='<?=($options['site']['company_name'])?$options['site']['company_name']:$siteList[1]["SITE_NAME"]?>' id='company_name' name='company_name' /></td>
			</tr>
			<tr>
				<td><label for='server_name'><?=GetMessage('ITHIVE_INSTALL_SITE')?></label></td>
				<td><input type='text' value='<?=($options['site']['server_name'])?$options['site']['server_name']:$siteList[1]["SERVER_NAME"]?>' id='server_name' name='server_name' /></td>
			</tr-->
			<tr>
				<td><label for='iblocks_to_export[]'><?=GetMessage('ITHIVE_IBLOCKS_TO_EXPORT')?></label></td>
				<td>
					<select name='iblocks_to_export[]' multiple size='4'>
					<?$count = 0;
					while($arRes = $dbResult->Fetch()) {
						if(CCatalogSKU::GetInfoByOfferIBlock($arRes['ID']))continue;
					?>
						<option value='<?=$arRes['ID']?>' <?=((!$options['iblocks']) && ($count == 0))?'selected':''?><?=(@in_array($arRes['ID'], $options['iblocks']))?'selected':'';?>><?='['.$arRes['IBLOCK_TYPE_ID'].'] '.$arRes['NAME']?></option>
					<?$count++;
					}?>
					</select>
				</td>
			</tr>
			<tr>
				<td><label for='price_types'><?=GetMessage('ITHIVE_PRICE_TYPES_TO_EXPORT')?></label></td>
				<td>
					<select name='price_types'>
					<?while ($arPriceType = $dbPriceType->Fetch()) {?>
						<option value='<?=$arPriceType['ID']?>' <?=($arPriceType['BASE'] == 'Y')?'selected':'';?>><?='['.$arPriceType['NAME'].'] '.$arPriceType['NAME_LANG']?></option>
					<?}?>
					</select>
				</td>
			</tr>
			
			
		</table>
		
		<input type="submit" name="inst" value="<?= GetMessage("NEXT_STEP")?>" class="adm-btn-save">
	</form>
	<?
}
?>