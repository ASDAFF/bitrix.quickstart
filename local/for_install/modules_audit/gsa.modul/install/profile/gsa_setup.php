<?
include($_SERVER["DOCUMENT_ROOT"].BX_ROOT.'/modules/gsa.modul/lang/ru/all.php');

$strMyError = "";
global $APPLICATION;
if ($STEP>1)
{
	if ( !is_array($IBLOCKS) || count($IBLOCKS) <= 0 ) {
		$strMyError .= GetMessage("GSA_ERSELECT");
	}

	if (strlen($SETUP_FILE_NAME) <= 0)
		$strMyError .= GetMessage("GSA_ERFILE");

	if ($ACTION=="EXPORT_SETUP" && strlen($SETUP_PROFILE_NAME)<=0)
		$strMyError .= GetMessage("GSA_ERPROFILE");

	if (strlen($strMyError) > 0)
	{
		$STEP = 1;
	}
}

echo ShowError($strMyError);

if ($STEP==1)
{
	?>
<form method="post" action="<?=$APPLICATION->GetCurPage()?>">
    <?=bitrix_sessid_post()?>
    <?=GetMessage("GSA_SELECTIBLOCK")?>
			<?
			if (CModule::IncludeModule('iblock'))
			{
			// работаем с классами модуля
			$db_res = CIBlock::GetList(Array("iblock_type"=>"asc", "name"=>"asc"));
			}
			?>
				<table border="1">
					<thead>
						<tr>
							<th><?=GetMessage("GSA_GIBLOCK")?></th>
							<th><?=GetMessage("GSA_OIBLOCK")?></th>
							<th><?=GetMessage("GSA_ADDGIMG")?></th>
							<th><?=GetMessage("GSA_ADDOIMG")?></th>
						</tr>
					</thead>
					<tbody>

			<?
			while ($res = $db_res->Fetch())
			{


				if (CCatalogSKU::GetInfoByOfferIBlock($res["ID"])) continue;
				$ioffersid = false;
				$ioffersname = false;
				$iblockid = $res["ID"];
				$iblockname = $res["NAME"];

				$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$iblockid, "PROPERTY_TYPE" => "F"));
				$iblockPropString = "<select name='IBLOCK_PHOTOS[]'>";
				$many = 0;
				while ($prop_fields = $properties->GetNext())
				{
				  	$iblockPropString .= "<option value='".$iblockid."-".$prop_fields["ID"]."'>".$prop_fields["NAME"]."</option>";
				  	$many++;
				}
				if ($many) {
					$iblockPropString .= "</select>";
				} else {
					$iblockPropString = GetMessage("GSA_NOTYPEFILE");
				}


				$arIblockInfo = CCatalog::GetByID($iblockid);
				if (!empty($arIblockInfo))
				{
					$arOffers = CCatalogSKU::GetInfoByProductIBlock($iblockid);
					if ($arOffers["IBLOCK_ID"]) {
						$arOffersInfo = CCatalog::GetByID($arOffers["IBLOCK_ID"]);
						if ($arOffersInfo) {
							$ioffersid = $arOffersInfo["ID"];
							$ioffersname = $arOffersInfo["NAME"];

							$properties = CIBlockProperty::GetList(Array("sort"=>"asc", "name"=>"asc"), Array("ACTIVE"=>"Y", "IBLOCK_ID"=>$ioffersid, "PROPERTY_TYPE" => "F"));
							$offersPropString = "<select name='OFFER_PHOTOS[]'>";
							$many = 0;
							while ($prop_fields = $properties->GetNext())
							{
							  	$offersPropString .= "<option value='".$ioffersid."-".$prop_fields["ID"]."'>".$prop_fields["NAME"]."</option>";
							  	$many++;
							}
							if ($many) {
								$offersPropString .= "</select>";
							} else {
								$offersPropString = GetMessage("GSA_NOTYPEFILE");
							}

						}
					}
					?>
						<tr>
							<td><input type="checkbox" name="IBLOCKS[]" value="<?=$iblockid?>" id="iblock<?=$iblockid?>"><label for="iblock<?=$iblockid?>"><?=$iblockname." [".$iblockid."]"?></label></td>
							<td>
								<?if ($ioffersid && $ioffersname):?>
									<?=$ioffersname?>
								<?endif?>
							</td>
							<td><?=$iblockPropString?></td>
							<td><?=$offersPropString?></td>
						</tr>

					<?
				}
			}
			?>
					</tbody>
				</table>

		<br>

		<!-- тип цен -->
		<?
			//типы цен

			// Выберем типы цен с внутренним именем retail
			$dbPriceType = CCatalogGroup::GetList(
			        array("SORT" => "ASC"),
			        array(),
			        false,
			        false,
			        array("NAME_LANG", "ID")
			    );
		?>
		<br>
		<?=GetMessage("GSA_SELECTPRICE")?> <br>
		<select name="PRICE_TYPE">
		<?
			while ($arPriceType = $dbPriceType->Fetch())
			{
			    echo "<option value=".$arPriceType["ID"].">".$arPriceType["NAME_LANG"]."</option>";
			}

		?>
		</select>
		<br>

		<?=GetMessage("GSA_SELECTNAME")?>
		<input type="text" name="SETUP_FILE_NAME"
		 value="<?echo (strlen($SETUP_FILE_NAME)>0) ?
						htmlspecialchars($SETUP_FILE_NAME) :
						"/upload/file.csv" ?>" size="50">
		<br>

		<?if ($ACTION=="EXPORT_SETUP"):?>
			<?=GetMessage("GSA_SELECTPROFILE")?>
			<input type="text" name="SETUP_PROFILE_NAME"
			 value="<?echo htmlspecialchars($SETUP_PROFILE_NAME)?>"
			 size="30">
			<br>
		<?endif;?>

		<?//Следующие переменные должны быть обязательно установлены?>
		<input type="hidden" name="lang" value="<?echo $lang ?>">
		<input type="hidden" name="ACT_FILE"
		 value="<?echo htmlspecialchars($_REQUEST["ACT_FILE"]) ?>">
		<input type="hidden" name="ACTION" value="<?echo $ACTION ?>">
		<input type="hidden" name="STEP" value="<?echo $STEP + 1 ?>">
		<input type="hidden" name="SETUP_FIELDS_LIST"
		 value="IBLOCK_ID,SETUP_FILE_NAME, IBLOCKS, PRICE_TYPE, OFFER_PHOTOS, IBLOCK_PHOTOS">
		<input type="submit"
		 value="<?echo ($ACTION=="EXPORT") ?
						GetMessage("GSA_EXPORT") :
						GetMessage("GSA_SAVE");?>">
	</form>
	<?
}
elseif ($STEP==2)
{
	// Второй шаг не нужен, говорим "передать управление дальше"
	$FINITE = True;
}
?>