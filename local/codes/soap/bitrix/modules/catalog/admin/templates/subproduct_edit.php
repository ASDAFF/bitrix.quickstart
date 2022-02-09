<?
if ($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_price'))
{
	include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/templates/product_edit.php"));

	$IBLOCK_ID = intval($IBLOCK_ID);
	if ($IBLOCK_ID <= 0)
		return;
	$arCatalog = CCatalog::GetByID($IBLOCK_ID);
	$PRODUCT_ID = (0 < $ID ? CIBlockElement::GetRealElement($ID) : 0);
	$arBaseProduct = CCatalogProduct::GetByID($PRODUCT_ID);

	if (0 < $PRODUCT_ID)
	{
		$bReadOnly = !($USER->CanDoOperation('catalog_price') && CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $PRODUCT_ID, "element_edit_price"));
	}
	else
	{
		$bReadOnly = !($USER->CanDoOperation('catalog_price') && CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "element_edit_price"));
	}
	$bDiscount = $USER->CanDoOperation('catalog_discount');
?>
<style type="text/css">
input.wrong {background-color: #FF8080;}
</style>
<tr class="heading">
	<td colspan="2"><?
	echo GetMessage("IBLOCK_TCATALOG");
	if ($bReadOnly) echo " ".GetMessage("IBLOCK_TREADONLY");
	?><script type="text/javascript">
var bReadOnly = <? echo ($bReadOnly ? 'true' : 'false'); ?>;
function getElementSubForm()
{
	for(var i = 0; i < document.forms.length; i++)
	{
		var check = document.forms[i].name.substring(0, 10).toUpperCase();
		if(check == 'FORM_ELEME' || check == 'TABCONTROL')
			return document.forms[i];
	}
}
function getElementSubFormName()
{
	var form = getElementSubForm();
	if (form)
		return form.name;
	else
		return '';
}
function checkSubForm(e)
{
	if (window.BX_CANCEL)
		return true;

	if (!e)
		e = window.event;

	var bReturn = true;

	if (BX('SUBCAT_ROW_COUNTER').value > 0 && !!BX('subprice_useextform') && !BX('subprice_useextform').checked)
	{
		bReturn = confirm('<?=CUtil::JSEscape(GetMessage("CAT_E_PRICE_EXT"))?>');
	}
	if (!bReturn)
	{
		if (e.preventDefault)
			e.preventDefault();

		return false;
	}

	return true;
}

jsUtils.addEvent(window, 'load', function () {
	var obForm = getElementSubForm();
	jsUtils.addEvent(obForm, 'submit', checkSubForm);
	jsUtils.addEvent(obForm.dontsave, 'click', function() {window.BX_CANCEL = true; setTimeout('window.BX_CANCEL = false', 10);});
});
</script>
	</td>
</tr>
<tr>
	<td valign="top" colspan="2">
<script type="text/javascript">
function SetSubFieldsStyle(table_id)
{
	var tbl = BX(table_id);
	var n = tbl.rows.length;
	for(var i=0; i<n; i++)
		if(tbl.rows[i].cells[0].colSpan == 1)
			tbl.rows[i].cells[0].className = 'field-name';
}
</script>
		<?
		$aTabs1 = array();
		$aTabs1[] = array("DIV" => "subcat_edit1", "TAB" => GetMessage("C2IT_PRICES"), "TITLE" => GetMessage("C2IT_PRICES_D"));

		$aTabs1[] = array("DIV" => "subcat_edit3", "TAB" => GetMessage("C2IT_PARAMS"), "TITLE" => GetMessage("C2IT_PARAMS_D"));
		if($arCatalog["SUBSCRIPTION"] == "Y")
			$aTabs1[] = array("DIV" => "subcat_edit4", "TAB" => GetMessage("C2IT_GROUPS"), "TITLE" => GetMessage("C2IT_GROUPS_D"));
		if(CBXFeatures::IsFeatureEnabled('CatMultiStore'))
			$aTabs1[] = array("DIV" => "subcat_edit5", "TAB" => GetMessage("C2IT_STORE"), "TITLE" => GetMessage("C2IT_STORE_D"));
		$aTabs1[] = array("DIV" => "subcat_edit6", "TAB" => GetMessage("C2IT_DISCOUNTS"), "TITLE" => GetMessage("C2IT_DISCOUNTS_D"));

		$subtabControl1 = new CAdminViewTabControl("subtabControl1", $aTabs1);
		$subtabControl1->Begin();

		// Define boundaries
		$arPriceBoundariesError = array();
		$arPriceBoundaries = array();
		$dbPrice = CPrice::GetList(
				array("BASE" => "DESC", "CATALOG_GROUP_ID" => "ASC", "QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
				array("PRODUCT_ID" => $PRODUCT_ID)
			);
		while ($arPrice = $dbPrice->Fetch())
		{
			if ($arPrice["BASE"] == "Y")
			{
				$arPriceBoundaries[] = array(
						"FROM" => intval($arPrice["QUANTITY_FROM"]),
						"TO" => intval($arPrice["QUANTITY_TO"])
					);
				if (intval($arPrice["QUANTITY_FROM"]) > intval($arPrice["QUANTITY_TO"])
					&& intval($arPrice["QUANTITY_TO"]) != 0)
				{
					$arPriceBoundariesError[] = str_replace("#RIGHT#", $arPrice["QUANTITY_TO"], str_replace("#LEFT#", $arPrice["QUANTITY_FROM"], GetMessage("C2IT_BOUND_LR")));
				}
			}
			else
			{
				if (intval($arPrice["QUANTITY_FROM"]) > intval($arPrice["QUANTITY_TO"])
					&& intval($arPrice["QUANTITY_TO"]) != 0)
				{
					$arPriceBoundariesError[] = str_replace("#TYPE#", $arPrice["CATALOG_GROUP_NAME"], str_replace("#RIGHT#", $arPrice["QUANTITY_TO"], str_replace("#LEFT#", $arPrice["QUANTITY_FROM"], GetMessage("C2IT_BOUND_LR1"))));
				}
				else
				{
					$bNewSegment = true;
					$intCount = count($arPriceBoundaries);
					for ($i = 0; $i < $intCount; $i++)
					{
						if ($arPriceBoundaries[$i]["FROM"] == intval($arPrice["QUANTITY_FROM"]))
						{
							if ($arPriceBoundaries[$i]["TO"] != intval($arPrice["QUANTITY_TO"]))
							{
								$arPriceBoundariesError[] = str_replace("#TYPE#", $arPrice["CATALOG_GROUP_NAME"], str_replace("#RIGHT#", $arPrice["QUANTITY_TO"], str_replace("#LEFT#", $arPrice["QUANTITY_FROM"], GetMessage("C2IT_BOUND_DIAP"))));
							}
							$bNewSegment = false;
							break;
						}
						else
						{
							if ($arPriceBoundaries[$i]["FROM"] < intval($arPrice["QUANTITY_FROM"])
								&& $arPriceBoundaries[$i]["TO"] >= intval($arPrice["QUANTITY_TO"])
								&& intval($arPrice["QUANTITY_TO"]) != 0)
							{
								$arPriceBoundariesError[] = str_replace("#TYPE#", $arPrice["CATALOG_GROUP_NAME"], str_replace("#RIGHT#", $arPrice["QUANTITY_TO"], str_replace("#LEFT#", $arPrice["QUANTITY_FROM"], GetMessage("C2IT_BOUND_DIAP"))));
								$bNewSegment = false;
								break;
							}
						}
					}
					if ($bNewSegment)
					{
						$arPriceBoundaries[] = array("FROM" => intval($arPrice["QUANTITY_FROM"]), "TO" => intval($arPrice["QUANTITY_TO"]));
					}
				}
			}
		}

		$intCount = count($arPriceBoundaries);
		for ($i = 0; $i < $intCount - 1; $i++)
		{
			for ($j = $i + 1; $j < $intCount; $j++)
			{
				if ($arPriceBoundaries[$i]["FROM"] > $arPriceBoundaries[$j]["FROM"])
				{
					$tmp = $arPriceBoundaries[$i];
					$arPriceBoundaries[$i] = $arPriceBoundaries[$j];
					$arPriceBoundaries[$j] = $tmp;
				}
			}
		}
		?>
<script type="text/javascript">
function toggleSubPriceType()
{
	var obSubPriceSimple = BX('subprices_simple');
	var obSubPriceExt = BX('subprices_ext');
	var obSubBasePrice = BX('tr_SUB_BASE_PRICE');
	var obSubBaseCurrency = BX('tr_SUB_BASE_CURRENCY');

	if (obSubPriceSimple.style.display == 'block')
	{
		obSubPriceSimple.style.display = 'none';
		obSubPriceExt.style.display = 'block';
		if (!!obSubBasePrice)
			BX.style(obSubBasePrice, 'display', 'none');
		if (!!obSubBaseCurrency)
			BX.style(obSubBaseCurrency, 'display', 'none');
	}
	else
	{
		obSubPriceSimple.style.display = 'block';
		obSubPriceExt.style.display = 'none';
		if (!!obSubBasePrice)
			BX.style(obSubBasePrice, 'display', 'table-row');
		if (!!obSubBaseCurrency)
			BX.style(obSubBaseCurrency, 'display', 'table-row');
	}
}
</script>
		<?
// prices tab
		$subtabControl1->BeginNextTab();
$arCatPricesExist = array(); // attr for exist prices for range
$bUseExtendedPrice = $bVarsFromForm ? $subprice_useextform == 'Y' : count($arPriceBoundaries) > 1;
$str_CAT_VAT_ID = $bVarsFromForm ? $SUBCAT_VAT_ID : ($arBaseProduct['VAT_ID'] == 0 ? $arCatalog['VAT_ID'] : $arBaseProduct['VAT_ID']);
$str_CAT_VAT_INCLUDED = $bVarsFromForm ? $SUBCAT_VAT_INCLUDED : $arBaseProduct['VAT_INCLUDED'];
		?>
<input type="hidden" name="subprice_useextform" id="subprice_useextform_N" value="N" />
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="subcatalog_vat_table">
<?
if (CBXFeatures::IsFeatureEnabled('CatMultiPrice'))
{
?>
	<tr>
		<td width="40%"><label for="subprice_useextform"><? echo GetMessage('C2IT_PRICES_USEEXT'); ?>:</label></td>
		<td width="60%">
			<input type="checkbox" name="subprice_useextform" id="subprice_useextform" value="Y" onclick="toggleSubPriceType()" <?=$bUseExtendedPrice ? 'checked="checked"' : ''?> <? echo ((!CBXFeatures::IsFeatureEnabled('CatMultiPrice') || $bReadOnly) ? ' disabled readonly' : ''); ?> />
		</td>
	</tr>
<?
}
?>
	<tr>
		<td width="40%">
			<?echo GetMessage("CAT_VAT")?>:
		</td>
		<td width="60%">
<?
	$arVATRef = CatalogGetVATArray(array(), true);
	echo SelectBoxFromArray('SUBCAT_VAT_ID', $arVATRef, $str_CAT_VAT_ID, "", $bReadOnly ? "disabled readonly" : '');
?>
		</td>
	</tr>
	<tr>
		<td width="40%"><label for="SUBCAT_VAT_INCLUDED"><? echo GetMessage("CAT_VAT_INCLUDED");?></label>:</td>
		<td width="60%">
			<input type="hidden" name="SUBCAT_VAT_INCLUDED" id="SUBCAT_VAT_INCLUDED_N" value="N">
			<input type="checkbox" name="SUBCAT_VAT_INCLUDED" id="SUBCAT_VAT_INCLUDED" value="Y" <?=$str_CAT_VAT_INCLUDED == 'Y' ? 'checked="checked"' : ''?> <?=$bReadOnly ? 'disabled readonly' : ''?> />
		</td>
	</tr>
			<tr id="tr_SUB_BASE_PRICE" style="display: <? echo ($bUseExtendedPrice ? 'none' : 'table-row'); ?>;">
				<td width="40%">
					<?
					$arBaseGroup = CCatalogGroup::GetBaseGroup();
					$arBasePrice = CPrice::GetBasePrice($PRODUCT_ID, $arPriceBoundaries[0]["FROM"], $arPriceBoundaries[0]["TO"]);
					echo GetMessage("BASE_PRICE")?> (<? echo GetMessage('C2IT_PRICE_TYPE'); ?> "<? echo htmlspecialcharsbx(!empty($arBaseGroup['NAME_LANG']) ? $arBaseGroup['NAME_LANG'] : $arBaseGroup["NAME"]); ?>"):
				</td>
				<td width="60%">
					<script type="text/javascript">
						var arExtra = new Array();
						var arExtraPrc = new Array();
						<?
						$db_extras = CExtra::GetList(($by3="NAME"), ($order3="ASC"));
						$i = 0;
						while ($extras = $db_extras->Fetch())
						{
							echo "arExtra[".$i."]=".$extras["ID"].";";
							echo "arExtraPrc[".$i."]=".$extras["PERCENTAGE"].";";
							$i++;
						}
						?>

						function OnChangeSubExtra(priceType)
						{
							if (bReadOnly)
								return;

							var e_base_price = BX('SUBCAT_BASE_PRICE');
							var e_extra = BX('SUBCAT_EXTRA_' + priceType);
							var e_price = BX('SUBCAT_PRICE_' + priceType);
							var e_currency = BX('SUBCAT_CURRENCY_' + priceType);

							if (isNaN(e_base_price.value) || e_base_price.value <= 0)
							{
								e_currency.disabled = false;
								e_price.disabled = false;
								return;
							}

							var i, esum, eps;
							if (parseInt(e_extra.selectedIndex)==0)
							{
								e_currency.disabled = false;
								e_price.disabled = false;
							}
							else
							{
								e_currency.selectedIndex = 0;
								e_currency.disabled = true;
								e_price.disabled = true;
								for (i = 0; i < arExtra.length; i++)
								{
									if (parseInt(e_extra.options[e_extra.selectedIndex].value) == parseInt(arExtra[i]))
									{
										esum = parseFloat(e_base_price.value) * (1 + arExtraPrc[i] / 100);
										eps = 1.00/Math.pow(10, 6);
										e_price.value = Math.round((esum+eps)*100)/100;
										break;
									}
								}
							}
						}


						function OnChangeSubExtraEx(e)
						{
							if (bReadOnly)
								return;

							var thename = e.name;

							var pos = thename.lastIndexOf("_");
							var ind = thename.substr(pos + 1);
							thename = thename.substr(0, pos);
							pos = thename.lastIndexOf("_");
							var ptype = thename.substr(pos + 1);

							var e_ext = BX('SUBCAT_EXTRA_'+ptype+"_"+ind);
							var e_price = BX('SUBCAT_PRICE_'+ptype+"_"+ind);
							var e_currency = BX('SUBCAT_CURRENCY_'+ptype+"_"+ind);

							var e_base_price = BX('SUBCAT_BASE_PRICE_'+ind);

							if (isNaN(e_base_price.value) || e_base_price.value <= 0)
							{
								e_price.disabled = false;
								e_currency.disabled = false;
								return;
							}

							var i, esum;
							if (parseInt(e_ext.selectedIndex)==0)
							{
								e_price.disabled = false;
								e_currency.disabled = false;
							}
							else
							{
								e_currency.selectedIndex = 0;
								e_currency.disabled = true;
								e_price.disabled = true;
								for (i = 0; i < arExtra.length; i++)
								{
									if (parseInt(e_ext.options[e_ext.selectedIndex].value) == parseInt(arExtra[i]))
									{
										esum = parseFloat(e_base_price.value) * (1 + arExtraPrc[i] / 100);
										eps = 1.00/Math.pow(10, 6);
										e_price.value = Math.round((esum+eps)*100)/100;
										break;
									}
								}
							}
						}

						function ChangeSubExtra(codID)
						{
							if (bReadOnly)
								return;

							OnChangeSubExtra(codID);

							var e_extra = BX('SUBCAT_EXTRA_' + codID + '_0');
							if (e_extra)
							{
								var e_extra_s = BX('SUBCAT_EXTRA_' + codID);
								e_extra.selectedIndex = e_extra_s.selectedIndex;
								OnChangeSubExtraEx(e_extra);
							}
						}

						function OnChangeSubBasePrice()
						{
							if (bReadOnly)
								return;

							var e_base_price = BX('SUBCAT_BASE_PRICE');

							if (isNaN(e_base_price.value) || e_base_price.value <= 0)
							{
								var k;
								for (k = 0; k < arCatalogGroups.length; k++)
								{
									e_price = BX('SUBCAT_PRICE_' + arCatalogGroups[k]);
									e_price.disabled = false;
									e_currency = BX('SUBCAT_CURRENCY_' + arCatalogGroups[k]);
									e_currency.disabled = false;
								}
								OnChangeSubPriceExist();
								return;
							}

							var i, j, esum, eps;
							var e_price;
							for (i = 0; i < arCatalogGroups.length; i++)
							{
								e_extra = BX('SUBCAT_EXTRA_' + arCatalogGroups[i]);
								if (e_extra.selectedIndex > 0)
								{
									e_price = BX('SUBCAT_PRICE_' + arCatalogGroups[i]);
									e_currency = BX('SUBCAT_CURRENCY_' + arCatalogGroups[i]);

									for (j = 0; j < arExtra.length; j++)
									{
										if (parseInt(e_extra.options[e_extra.selectedIndex].value) == parseInt(arExtra[j]))
										{
											esum = parseFloat(e_base_price.value) * (1 + arExtraPrc[j] / 100);
											eps = 1.00/Math.pow(10, 6);
											e_price.value = Math.round((esum+eps)*100)/100;
											e_currency.selectedIndex = 0;
											e_currency.disabled = true;
											e_price.disabled = true;
											break;
										}
									}
								}
							}
							OnChangeSubPriceExist();
						}

						function ChangeSubBasePrice(e)
						{
							if (bReadOnly)
								return;

							if (e.value != '' && (isNaN(e.value) || e.value <= 0))
							{
							}
							else
							{
								e.className = '';
							}

							OnChangeSubBasePrice();

							var e_base_price = BX('SUBCAT_BASE_PRICE_0');
							e_base_price.value = BX('SUBCAT_BASE_PRICE').value;
							OnChangeSubBasePriceEx(e_base_price);
							OnChangeSubPriceExistEx(e_base_price);
						}

						function ChangeSubBaseCurrency()
						{
							if (bReadOnly)
								return;

							BX('SUBCAT_BASE_CURRENCY_0').selectedIndex = BX('SUBCAT_BASE_CURRENCY').selectedIndex;
						}

						function ChangeSubPrice(codID)
						{
							if (bReadOnly)
								return;

							var e_price = BX('SUBCAT_PRICE_' + codID + '_0');
							e_price.value = BX('SUBCAT_PRICE_' + codID).value;
							OnChangeSubPriceExist();
							OnChangeSubPriceExistEx(e_price);
						}

						function ChangeSubCurrency(codID)
						{
							if (bReadOnly)
								return;

							var e_currency = BX('SUBCAT_CURRENCY_' + codID + "_0");
							e_currency.selectedIndex = BX('SUBCAT_CURRENCY_' + codID).selectedIndex;
						}

						function OnChangeSubPriceExist()
						{
							if (bReadOnly)
								return;

							var bExist = 'N';
							var e_price_exist = BX('SUBCAT_PRICE_EXIST');
							var e_ext_price_exist = BX('SUBCAT_PRICE_EXIST_0');
							var e_base_price = BX('SUBCAT_BASE_PRICE');

							if (isNaN(e_base_price.value) || e_base_price.value <= 0)
							{
								var i;
								var e_price;
								for (i = 0; i < arCatalogGroups.length; i++)
								{
									e_price = BX('SUBCAT_PRICE_' + arCatalogGroups[i]);
									if (!(isNaN(e_price.value) || e_price.value <= 0))
									{
										bExist = 'Y';
										break;
									}
								}
							}
							else
							{
								bExist = 'Y';
							}
							e_price_exist.value = bExist;
							e_ext_price_exist.value = bExist;
						}
					</script>

					<?
					$boolBaseExistPrice = false;
					$str_CAT_BASE_PRICE = "";
					if ($arBasePrice)
						$str_CAT_BASE_PRICE = $arBasePrice["PRICE"];
					if ($bVarsFromForm)
						$str_CAT_BASE_PRICE = $SUBCAT_BASE_PRICE;
					if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
						$boolBaseExistPrice = true;
					?>
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_BASE_PRICE" name="SUBCAT_BASE_PRICE" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="30" OnBlur="ChangeSubBasePrice(this)">
				</td>
			</tr>
			<tr id="tr_SUB_BASE_CURRENCY" style="display: <? echo ($bUseExtendedPrice ? 'none' : 'table-row'); ?>;">
				<td width="40%">
					<?echo GetMessage("BASE_CURRENCY")?>:
				</td>
				<td width="60%">
					<?
					if ($arBasePrice)
						$str_CAT_BASE_CURRENCY = $arBasePrice["CURRENCY"];
					if ($bVarsFromForm)
						$str_CAT_BASE_CURRENCY = $SUBCAT_BASE_CURRENCY;

					$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));
					?>
					<select id="SUBCAT_BASE_CURRENCY" name="SUBCAT_BASE_CURRENCY" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeSubBaseCurrency()">
						<?
						while ($curr = $db_curr->Fetch())
						{
							?><option value="<?echo $curr["CURRENCY"] ?>"<?if ($curr["CURRENCY"]==$str_CAT_BASE_CURRENCY) echo " selected"?>><?echo $curr["CURRENCY"]?> (<?echo htmlspecialcharsbx($curr["FULL_NAME"])?>)</option><?
						}
						?>
					</select>
				</td>
			</tr>
</table>
<script type="text/javascript">
SetSubFieldsStyle('subcatalog_vat_table');
</script>

<?
// simple price form
?>
<div id="subprices_simple" style="display: <?=$bUseExtendedPrice ? 'none' : 'block'?>;">
		<?
		$intCount = count($arPriceBoundariesError);
		if ($intCount > 0)
		{
			?>
			<font class="errortext">
			<?echo GetMessage("C2IT_BOUND_WRONG")?><br>
			<?
			for ($i = 0; $i < $intCount; $i++)
			{
				echo $arPriceBoundariesError[$i]."<br>";
			}
			?>
			<?echo GetMessage("C2IT_BOUND_RECOUNT")?>
			</font>
			<?
		}

	if (CBXFeatures::IsFeatureEnabled('CatMultiPrice'))
	{
		$bFirst = true;
		$dbCatalogGroups = CCatalogGroup::GetList(
				array("SORT" => "ASC","NAME" => "ASC","ID" => "ASC"),
				array("!BASE" => "Y")
			);

		while ($arCatalogGroup = $dbCatalogGroups->Fetch())
		{
			if($bFirst)
			{
				?>
			<br>
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
				<tr class="heading">
					<td><? echo GetMessage("PRICE_TYPE"); ?></td>
					<td><? echo GetMessage("PRICE_EXTRA"); ?></td>
					<td><? echo GetMessage("PRICE_SUM"); ?></td>
					<td><? echo GetMessage("PRICE_CURRENCY"); ?></td>
				</tr>
				<?
				$bFirst = false;
			}
			$str_CAT_EXTRA = 0;
			$str_CAT_PRICE = "";
			$str_CAT_CURRENCY = "";

			$dbPriceList = CPrice::GetList(
				array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
				array(
					"PRODUCT_ID" => $PRODUCT_ID,
					"CATALOG_GROUP_ID" => $arCatalogGroup["ID"],
					"QUANTITY_FROM" => $arPriceBoundaries[0]["FROM"],
					"QUANTITY_TO" => $arPriceBoundaries[0]["TO"]
				)
			);
			if ($arPrice = $dbPriceList->Fetch())
			{
				$str_CAT_EXTRA = $arPrice["EXTRA_ID"];
				$str_CAT_PRICE = $arPrice["PRICE"];
				$str_CAT_CURRENCY = $arPrice["CURRENCY"];
			}
			if ($bVarsFromForm)
			{
				$str_CAT_EXTRA = ${"SUBCAT_EXTRA_".$arCatalogGroup["ID"]};
				$str_CAT_PRICE = ${"SUBCAT_PRICE_".$arCatalogGroup["ID"]};
				$str_CAT_CURRENCY = ${"SUBCAT_CURRENCY_".$arCatalogGroup["ID"]};
			}
			if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
				$boolBaseExistPrice = true;
			?>
			<tr <?if ($bReadOnly) echo "disabled" ?>>
				<td valign="top" align="left">
					<? echo htmlspecialcharsbx(!empty($arCatalogGroup["NAME_LANG"]) ? $arCatalogGroup["NAME_LANG"] : $arCatalogGroup["NAME"]); ?>
					<?if ($arPrice):?>
					<input type="hidden" name="SUBCAT_ID_<?echo $arCatalogGroup["ID"] ?>" value="<?echo $arPrice["ID"] ?>">
					<?endif;?>
				</td>
				<td valign="top" align="center">
					<?
					echo CExtra::SelectBox("SUBCAT_EXTRA_".$arCatalogGroup["ID"], $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeSubExtra(".$arCatalogGroup["ID"].")", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_EXTRA_".$arCatalogGroup["ID"].'" ');
					?>
				</td>
				<td valign="top" align="center">
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_PRICE_<?echo $arCatalogGroup["ID"] ?>" name="SUBCAT_PRICE_<?echo $arCatalogGroup["ID"] ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="8" OnChange="ChangeSubPrice(<?= $arCatalogGroup["ID"] ?>)">
				</td>
				<td valign="top" align="center">
					<?
					echo CCurrency::SelectBox("SUBCAT_CURRENCY_".$arCatalogGroup["ID"], $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeSubCurrency(".$arCatalogGroup["ID"].")", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_CURRENCY_".$arCatalogGroup["ID"].'" ')
					?>
					<script type="text/javascript">
						ChangeSubExtra(<?echo $arCatalogGroup["ID"] ?>);
					</script>
				</td>
			</tr>
			<?
		}// endwhile
		if (!$bFirst) echo "</table>";
	}
		?><input type="hidden" name="SUBCAT_PRICE_EXIST" id="SUBCAT_PRICE_EXIST" value="<? echo ($boolBaseExistPrice == true ? 'Y' : 'N'); ?>">
</div>
		<?
		//$subtabControl1->BeginNextTab();
// extended price form
		?>
<div id="subprices_ext" style="display: <?=$bUseExtendedPrice ? 'block' : 'none'?>;">
		<script type="text/javascript">
		function addSubNewElementsGroup(parentId, modelId, counterId, keepValues, typefocus)
		{
			if (bReadOnly)
				return;

			if (!BX(counterId))
				return false;
			var n = ++BX(counterId).value;
			var thebody = BX(parentId);
			if (!thebody)
				return false;
			var therow = BX(modelId);
			if (!therow)
				return false;
			var thecopy = duplicateSubElement(therow, n, keepValues);
			thebody.appendChild(thecopy);

			return true;
		}

		function duplicateSubElement(e, n, keepVal)
		{
			if (bReadOnly)
				return;

			if (typeof e.tagName != "undefined")
			{
				var copia = document.createElement(e.tagName);

				var attr = e.attributes;
				if (attr)
				{
					for (i=0; i<attr.length; i++)
					{
						copia.setAttribute(attr[i].name, attr[i].value);
					}
				}

				if (e.id) copia.id = e.id + n;
				if (e.text) copia.text = e.text;

				if (e.tagName.toLowerCase() == "textarea" && !keepVal)
				{
					copia.text = "";
				}
				if (e.name)
				{
					var thename = e.name;

					if (thename.substr(thename.length-1)!="]")
					{
						var ind = thename.lastIndexOf("_");
						if (ind > -1)
						{
							var thename_postf = thename.substr(ind + 1);
							if (!isNaN(parseFloat(thename_postf)))
							{
								thename = thename.substring(0, ind);
							}
						}
						thename = thename + "_" + n;
					}
					else
					{
						var ind = thename.indexOf("[");
						if (ind > -1)
						{
							thename = thename.substring(0, ind);
							thename = thename + "[" + n + "]";
						}
					}

					copia.name = thename;
				}

				copia.value = ((keepVal == true) ?  e.value : ((e.tagName.toLowerCase() == "option" || e.type == "button") ? e.value : null));

				var hijos = e.childNodes;
				if (hijos)
				{
					for (key in hijos)
					{
						if (typeof hijos[key] != "undefined")
						{
							hijocopia = duplicateSubElement(hijos[key], n, keepVal);
							if (hijocopia) copia.appendChild(hijocopia);
						}
					}
				}
				return copia;
			}
			return null;
		}

		function CloneSubBasePriceGroup()
		{
			if (bReadOnly)
				return;

			var oTbl = BX("SUBBASE_PRICE_GROUP_TABLE");
			if (!oTbl)
				return;

			var oCntr = BX("SUBCAT_ROW_COUNTER");
			var cnt = parseInt(oCntr.value);
			cnt = cnt + 1;

			var oRow = oTbl.insertRow(-1);
			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_FROM_'+cnt+'" value="" size="3" OnChange="ChangeSubBaseQuantityEx(this)">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_TO_'+cnt+'" value="" size="3" OnChange="ChangeSubBaseQuantityEx(this)">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_BASE_PRICE_'+cnt+'" name="SUBCAT_BASE_PRICE_'+cnt+'" value="" size="15" OnBlur="ChangeSubBasePriceEx(this)">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			var str = '';
			<?$dbCurrencyList = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
			str = '<select id="SUBCAT_BASE_CURRENCY_'+cnt+'" name="SUBCAT_BASE_CURRENCY_'+cnt+'" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeSubBaseCurrencyEx(this)">';
			<?
			while ($arCurrencyList = $dbCurrencyList->Fetch())
			{
				?>str += '<option value="<?echo $arCurrencyList["CURRENCY"] ?>"><?echo $arCurrencyList["CURRENCY"]?> (<?echo CUtil::JSEscape(htmlspecialcharsbx($arCurrencyList["FULL_NAME"]))?>)</option>';<?
			}
			?>
			str += '</select>';
			oCell.innerHTML = str;

			var div_ext_price_exist = BX('ext_subprice_exist');
			var new_price_exist = BX.create('input',
											{'attrs': {
												'type': 'hidden',
												'name': 'SUBCAT_PRICE_EXIST_'+cnt,
												'value': 'N'
												}
											});
			new_price_exist.id = 'SUBCAT_PRICE_EXIST_'+cnt,
			div_ext_price_exist.appendChild(new_price_exist);
			oCntr.value = cnt;
		}

		function CloneSubOtherPriceGroup(ind)
		{
			if (bReadOnly)
				return;

			var oTbl = BX("SUBOTHER_PRICE_GROUP_TABLE_"+ind);
			if (!oTbl)
				return;

			var oCntr = BX("SUBCAT_ROW_COUNTER_"+ind);
			var cnt = parseInt(oCntr.value);
			cnt = cnt + 1;

			var oRow = oTbl.insertRow(-1);
			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" disabled readonly id="SUBCAT_QUANTITY_FROM_'+ind+'_'+cnt+'" name="SUBCAT_QUANTITY_FROM_'+ind+'_'+cnt+'" value="" size="3">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" disabled readonly id="SUBCAT_QUANTITY_TO_'+ind+'_'+cnt+'" name="SUBCAT_QUANTITY_TO_'+ind+'_'+cnt+'" value="" size="3">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			<?
			if (!isset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]) || !is_array($GLOBALS["MAIN_EXTRA_LIST_CACHE"]) || count($GLOBALS["MAIN_EXTRA_LIST_CACHE"])<1)
			{
				unset($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);
				$GLOBALS["MAIN_EXTRA_LIST_CACHE"] = array();
				$l = CExtra::GetList(($by="NAME"), ($order="ASC"));
				while ($l_res = $l->Fetch())
				{
					$GLOBALS["MAIN_EXTRA_LIST_CACHE"][] = $l_res;
				}
			}
			?>

			var str = '';
			oCell.valign = "top";
			oCell.align = "center";
			//oCell.className = "tablebody";
			str += '<select id="SUBCAT_EXTRA_'+ind+'_'+cnt+'" name="SUBCAT_EXTRA_'+ind+'_'+cnt+'" OnChange="ChangeSubExtraEx(this)" <?if ($bReadOnly) echo "disabled readonly" ?>>';
			str += '<option value=""><?= GetMessage("VAL_NOT_SET") ?></option>';
			<?
			$intCount = count($GLOBALS["MAIN_EXTRA_LIST_CACHE"]);
			for ($i = 0; $i < $intCount; $i++)
			{
				?>
				str += '<option value="<?= $GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["ID"] ?>"><?= CUtil::JSEscape(htmlspecialcharsbx($GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["NAME"]))." (".htmlspecialcharsbx($GLOBALS["MAIN_EXTRA_LIST_CACHE"][$i]["PERCENTAGE"])."%)" ?></option>';
				<?
			}
			?>
			str += '</select>';
			oCell.innerHTML = str;

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_PRICE_'+ind+'_'+cnt+'" name="SUBCAT_PRICE_'+ind+'_'+cnt+'" value="" size="10" OnChange="ptSubPriceChangeEx(this)">';

			var oCell = oRow.insertCell(-1);
			oCell.valign = "top";
			oCell.align = "center";
			var str = '';
			<?$dbCurrencyList = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
			str += '<select id="SUBCAT_CURRENCY_'+ind+'_'+cnt+'" name="SUBCAT_CURRENCY_'+ind+'_'+cnt+'" OnChange="ChangeSubCurrencyEx(this)" <?if ($bReadOnly) echo "disabled readonly" ?>>';
			str += '<option value=""><?= GetMessage("VAL_BASE") ?></option>';
			<?
			while ($arCurrencyList = $dbCurrencyList->Fetch())
			{
				?>str += '<option value="<?echo $arCurrencyList["CURRENCY"] ?>"><?echo $arCurrencyList["CURRENCY"]?></option>';<?
			}
			?>
			str += '</select>';
			oCell.innerHTML = str;

			oCntr.value = cnt;
		}

		function CloneSubPriceSections()
		{
			if (bReadOnly)
				return;

			CloneSubBasePriceGroup();

			var i, n;
			for (i = 0; i < arCatalogGroups.length; i++)
			{
				CloneSubOtherPriceGroup(arCatalogGroups[i]);

				n = BX('SUBCAT_ROW_COUNTER_'+arCatalogGroups[i]).value;
				ChangeSubExtraEx(BX('SUBCAT_EXTRA_'+arCatalogGroups[i]+"_"+n));
			}
		}

		function ChangeSubBaseQuantityEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			var type;
			if (thename.substring(0, "SUBCAT_BASE_QUANTITY_FROM_".length) == "SUBCAT_BASE_QUANTITY_FROM_")
			{
				type = "FROM";
			}
			else
			{
				type = "TO";
			}

			var i;
			var quantity;

			for (i = 0; i < arCatalogGroups.length; i++)
			{
				quantity = BX('SUBCAT_QUANTITY_'+type+"_"+arCatalogGroups[i]+"_"+ind);
				quantity.value = e.value;
			}
		}

		function OnChangeSubBasePriceEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (isNaN(e.value) || e.value <= 0)
			{
				for (i = 0; i < arCatalogGroups.length; i++)
				{
					e_price = document.getElementById('SUBCAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
					e_price.disabled = false;
					e_cur = document.getElementById('SUBCAT_CURRENCY_'+arCatalogGroups[i]+"_"+ind);
					e_cur.disabled = false;
				}
				OnChangeSubPriceExistEx(e);
				return;
			}

			var i;
			var e_price, e_ext;

			for (i = 0; i < arCatalogGroups.length; i++)
			{
				e_price = BX('SUBCAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
				e_cur = BX('SUBCAT_CURRENCY_'+arCatalogGroups[i]+"_"+ind);
				e_ext = BX('SUBCAT_EXTRA_'+arCatalogGroups[i]+"_"+ind);

				if (!e_ext)
					continue;

				for (j = 0; j < arExtra.length; j++)
				{
					if (parseInt(e_ext.options[e_ext.selectedIndex].value) == parseInt(arExtra[j]))
					{
						esum = parseFloat(e.value) * (1 + arExtraPrc[j] / 100);
						eps = 1.00/Math.pow(10, 6);
						e_price.value = Math.round((esum+eps)*100)/100;
						e_price.disabled = true;
						e_cur.selectedIndex = 0;
						e_cur.disabled = true;
						break;
					}
				}
			}
			OnChangeSubPriceExistEx(e);
		}

		function ChangeSubBasePriceEx(e)
		{
			if (bReadOnly)
				return;

			if (isNaN(e.value) || e.value <= 0)
			{
			}
			else
			{
				e.className = '';
			}

			OnChangeSubBasePriceEx(e);

			var thename = e.name;
			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (parseInt(ind) == 0)
			{
				BX('SUBCAT_BASE_PRICE').value = e.value;
				OnChangeSubBasePrice();
				OnChangeSubPriceExist();
			}
		}

		function ChangeSubExtraEx(e)
		{
			if (bReadOnly)
				return;

			if (null == e)
				return;

			OnChangeSubExtraEx(e);
			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);
			thename = thename.substr(0, pos);
			pos = thename.lastIndexOf("_");
			var ptype = thename.substr(pos + 1);

			if (parseInt(ind) == 0)
			{
				BX('SUBCAT_EXTRA_'+ptype).selectedIndex = e.selectedIndex;
				OnChangeSubExtra(ptype);
			}
		}

		function ChangeSubBaseCurrencyEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (parseInt(ind) == 0)
			{
				BX('SUBCAT_BASE_CURRENCY').selectedIndex = e.selectedIndex;
			}
		}

		function ptSubPriceChangeEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (parseInt(ind) == 0)
			{
				thename = thename.substr(0, pos);
				pos = thename.lastIndexOf("_");
				var ptype = thename.substr(pos + 1);

				BX('SUBCAT_PRICE_'+ptype).value = e.value;
				OnChangeSubPriceExist();
			}
			OnChangeSubPriceExistEx(e);
		}

		function ChangeSubCurrencyEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (parseInt(ind) == 0)
			{
				thename = thename.substr(0, pos);
				pos = thename.lastIndexOf("_");
				var ptype = thename.substr(pos + 1);

				BX('SUBCAT_CURRENCY_'+ptype).selectedIndex = e.selectedIndex;
			}
		}

		function OnChangeSubPriceExistEx(e)
		{
			if (bReadOnly)
				return;

			var thename = e.name;

			var pos = thename.lastIndexOf("_");
			var ind = thename.substr(pos + 1);

			if (!(isNaN(ind) || parseInt(ind) < 0))
			{
				var price_ext = BX('SUBCAT_PRICE_EXIST_'+ind);
				if (!price_ext)
					return;

				var i;
				var e_price;
				bExist = 'N';
				e_price = BX('SUBCAT_BASE_PRICE_'+ind);
				if (!e_price)
					return;

				if (isNaN(e_price.value) || e_price.value <= 0)
				{
					for (i = 0; i < arCatalogGroups.length; i++)
					{
						e_price = document.getElementById('SUBCAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
						if (!(isNaN(e_price.value) || e_price.value <= 0))
						{
							bExist = 'Y';
							break;
						}
					}
				}
				else
				{
					bExist = 'Y';
				}
				price_ext.value = bExist;
			}
		}
		</script>

		<?
		$intCount = count($arPriceBoundariesError);
		if ($intCount > 0)
		{
			?>
			<font class="errortext">
			<?echo GetMessage("C2IT_BOUND_WRONG")?><br>
			<?
			for ($i = 0; $i < $intCount; $i++)
			{
				echo $arPriceBoundariesError[$i]."<br>";
			}
			?>
			<?echo GetMessage("C2IT_BOUND_RECOUNT")?>
			</font>
			<?
		}
		$boolExistPrice = false;
		?>
		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
			<tr>
				<td valign="top" align="right">
				<?
					echo GetMessage("BASE_PRICE")?> (<? echo GetMessage('C2IT_PRICE_TYPE'); ?> "<? echo htmlspecialcharsbx(!empty($arBaseGroup['NAME_LANG']) ? $arBaseGroup['NAME_LANG'] : $arBaseGroup["NAME"]); ?>"):
				</td>
				<td valign="top" align="left">
					<table border="0" cellspacing="1" cellpadding="3" id="SUBBASE_PRICE_GROUP_TABLE">
						<thead>
						<tr>
							<td align="center"><?echo GetMessage("C2IT_FROM")?></td>
							<td align="center"><?echo GetMessage("C2IT_TO")?></td>
							<td align="center"><?echo GetMessage("C2IT_PRICE")?></td>
							<td align="center"><?echo GetMessage("C2IT_CURRENCY")?></td>
						</tr>
						</thead>
						<tbody id="subcontainer3">
							<?
							$ind = -1;
							$dbBasePrice = CPrice::GetList(
									array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
									array("BASE" => "Y", "PRODUCT_ID" => $PRODUCT_ID)
								);
							$arBasePrice = $dbBasePrice->Fetch();

							$intCount = count($arPriceBoundaries);
							for ($i = 0; $i < $intCount; $i++)
							{
								$boolExistPrice = false;
								$ind++;
								$str_CAT_BASE_QUANTITY_FROM = $arPriceBoundaries[$i]["FROM"];
								$str_CAT_BASE_QUANTITY_TO = $arPriceBoundaries[$i]["TO"];

								if ($arBasePrice
									&& intval($arBasePrice["QUANTITY_FROM"]) == $arPriceBoundaries[$i]["FROM"])
								{
									$str_CAT_BASE_ID = $arBasePrice["ID"];
									$str_CAT_BASE_PRICE = $arBasePrice["PRICE"];
									$str_CAT_BASE_CURRENCY = $arBasePrice["CURRENCY"];

									$arBasePrice = $dbBasePrice->Fetch();
								}
								else
								{
									$str_CAT_BASE_ID = 0;
									$str_CAT_BASE_PRICE = "";
									$str_CAT_BASE_CURRENCY = "";
								}

								if ($bVarsFromForm)
								{
									$str_CAT_BASE_QUANTITY_FROM = ${"SUBCAT_BASE_QUANTITY_FROM_".$ind};
									$str_CAT_BASE_QUANTITY_TO = ${"SUBCAT_BASE_QUANTITY_TO_".$ind};
									$str_CAT_BASE_PRICE = ${"SUBCAT_BASE_PRICE_".$ind};
									$str_CAT_BASE_CURRENCY = ${"SUBCAT_BASE_CURRENCY_".$ind};
								}
								if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
									$boolExistPrice = true;
								$arCatPricesExist[$ind][$arBaseGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
								?>
								<tr id="submodel3">
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_FROM) : "") ?>" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
										<input type="hidden" name="SUBCAT_BASE_ID[<?= $ind ?>]" value="<?= htmlspecialcharsbx($str_CAT_BASE_ID) ?>">
									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_TO_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_TO) : "") ?>" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_BASE_PRICE_<?= $ind ?>" name="SUBCAT_BASE_PRICE_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="15" OnBlur="ChangeSubBasePriceEx(this)">
									</td>
									<td valign="top" align="center">
										<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
										<select id="SUBCAT_BASE_CURRENCY_<?= $ind ?>" name="SUBCAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeSubBaseCurrencyEx(this)">
											<?
											while ($curr = $db_curr->Fetch())
											{
												?><option value="<?echo $curr["CURRENCY"] ?>"<?if ($curr["CURRENCY"]==$str_CAT_BASE_CURRENCY) echo " selected"?>><?echo $curr["CURRENCY"]?> (<?echo htmlspecialcharsbx($curr["FULL_NAME"])?>)</option><?
											}
											?>
										</select>
									</td>
								</tr>
								<?
							}

							if ($bVarsFromForm && $ind < intval($SUBCAT_ROW_COUNTER))
							{
								for ($i = $ind + 1; $i <= intval($SUBCAT_ROW_COUNTER); $i++)
								{
									$boolExistPrice = false;
									$ind++;
									$str_CAT_BASE_QUANTITY_FROM = ${"SUBCAT_BASE_QUANTITY_FROM_".$ind};
									$str_CAT_BASE_QUANTITY_TO = ${"SUBCAT_BASE_QUANTITY_TO_".$ind};
									$str_CAT_BASE_PRICE = ${"SUBCAT_BASE_PRICE_".$ind};
									$str_CAT_BASE_CURRENCY = ${"SUBCAT_BASE_CURRENCY_".$ind};
									if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
										$boolExistPrice = true;
									$arCatPricesExist[$ind][$arBaseGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
									?>
									<tr id="submodel3">
										<td valign="top" align="center">
											<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_FROM) : "") ?>" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
											<input type="hidden" name="SUBCAT_BASE_ID[<?= $ind ?>]" value="<?= 0 ?>">
										</td>
										<td valign="top" align="center">
											<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_TO_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_TO) : "") ?>" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
										</td>
										<td valign="top" align="center">
											<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_BASE_PRICE_<?= $ind ?>" name="SUBCAT_BASE_PRICE_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="15" OnBlur="ChangeSubBasePriceEx(this)">
										</td>
										<td valign="top" align="center">
											<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
											<select id="SUBCAT_BASE_CURRENCY_<?= $ind ?>" name="SUBCAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeSubBaseCurrencyEx(this)">
												<?
												while ($curr = $db_curr->Fetch())
												{
													?><option value="<?echo $curr["CURRENCY"] ?>"<?if ($curr["CURRENCY"]==$str_CAT_BASE_CURRENCY) echo " selected"?>><?echo $curr["CURRENCY"]?> (<?echo htmlspecialcharsbx($curr["FULL_NAME"])?>)</option><?
												}
												?>
											</select>
										</td>
									</tr>
									<?
								}
							}
							if ($ind == -1)
							{
								$ind++;
								?>
								<tr id="submodel3">
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_QUANTITY_TO_<?= $ind ?>" value="" size="3" OnChange="ChangeSubBaseQuantityEx(this)">
									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_BASE_PRICE_<?= $ind ?>" name="SUBCAT_BASE_PRICE_<?= $ind ?>" value="" size="15" OnBlur="ChangeSubBasePriceEx(this)">
									</td>
									<td valign="top" align="center">
										<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
										<select id="SUBCAT_BASE_CURRENCY_<?= $ind ?>" name="SUBCAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeSubBaseCurrencyEx(this)">
											<?
											while ($curr = $db_curr->Fetch())
											{
												?><option value="<?echo $curr["CURRENCY"] ?>"><?echo $curr["CURRENCY"]?> (<?echo htmlspecialcharsbx($curr["FULL_NAME"])?>)</option><?
											}
											?>
										</select>
									</td>
								</tr>
								<?
								$arCatPricesExist[$ind][$arBaseGroup['ID']] = 'N';
							}
							?>
						</tbody>
					</table>
					<input type="hidden" name="SUBCAT_ROW_COUNTER" id="SUBCAT_ROW_COUNTER" value="<?= $ind ?>">
					<input type="button" value="<?echo GetMessage("C2IT_MORE")?>" OnClick="CloneSubPriceSections()">
				</td>
			</tr>
			<script type="text/javascript">
			arCatalogGroups = new Array();
			catalogGroupsInd = 0;
			</script>
			<?
			$dbCatalogGroups = CCatalogGroup::GetList(
					array("SORT" => "ASC","NAME" => "ASC","ID" => "ASC"),
					array("!BASE" => "Y")
				);
			while ($arCatalogGroup = $dbCatalogGroups->Fetch())
			{
				?>
				<script type="text/javascript">
				arCatalogGroups[catalogGroupsInd] = <?= $arCatalogGroup["ID"] ?>;
				catalogGroupsInd++;
				</script>
				<tr>
					<td valign="top" align="right">
						<?echo GetMessage("C2IT_PRICE_TYPE")?> "<? echo htmlspecialcharsbx(!empty($arCatalogGroup["NAME_LANG"]) ? $arCatalogGroup["NAME_LANG"] : $arCatalogGroup["NAME"]); ?>":
					</td>
					<td valign="top" align="left">
						<table border="0" cellspacing="1" cellpadding="3" id="SUBOTHER_PRICE_GROUP_TABLE_<?= $arCatalogGroup["ID"] ?>">
							<thead>
							<tr>
							<td align="center"><?echo GetMessage("C2IT_FROM")?></td>
							<td align="center"><?echo GetMessage("C2IT_TO")?></td>
							<td align="center"><?echo GetMessage("C2IT_NAC_TYPE")?></td>
							<td align="center"><?echo GetMessage("C2IT_PRICE")?></td>
							<td align="center"><?echo GetMessage("C2IT_CURRENCY")?></td>
							</tr>
							</thead>
							<tbody id="subcontainer3_<?= $arCatalogGroup["ID"] ?>">
							<?
							$ind = -1;
							$dbPriceList = CPrice::GetList(
									array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
									array("PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => $arCatalogGroup["ID"])
								);
							$arPrice = $dbPriceList->Fetch();
							$intCount = count($arPriceBoundaries);
							for ($i = 0; $i < $intCount; $i++)
							{
								$boolExistPrice = false;
								$ind++;
								$str_CAT_QUANTITY_FROM = $arPriceBoundaries[$i]["FROM"];
								$str_CAT_QUANTITY_TO = $arPriceBoundaries[$i]["TO"];

								if ($arPrice
									&& intval($arPrice["QUANTITY_FROM"]) == $arPriceBoundaries[$i]["FROM"])
								{
									$str_CAT_ID = $arPrice["ID"];
									$str_CAT_EXTRA = $arPrice["EXTRA_ID"];
									$str_CAT_PRICE = $arPrice["PRICE"];
									$str_CAT_CURRENCY = $arPrice["CURRENCY"];

									$arPrice = $dbPriceList->Fetch();
								}
								else
								{
									$str_CAT_ID = 0;
									$str_CAT_EXTRA = 0;
									$str_CAT_PRICE = "";
									$str_CAT_CURRENCY = "";
								}

								if ($bVarsFromForm)
								{
									$str_CAT_EXTRA = ${"SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind};
									$str_CAT_PRICE = ${"SUBCAT_PRICE_".$arCatalogGroup["ID"]."_".$ind};
									$str_CAT_CURRENCY = ${"SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind};
									$str_CAT_QUANTITY_FROM = ${"SUBCAT_BASE_QUANTITY_FROM_".$ind};
									$str_CAT_QUANTITY_TO = ${"SUBCAT_BASE_QUANTITY_TO_".$ind};
								}
								if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
									$boolExistPrice = true;
								$arCatPricesExist[$ind][$arCatalogGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
								?>
								<tr id="submodel3_<?= $arCatalogGroup["ID"] ?>">
									<td valign="top" align="center">
										<input type="text" disabled readonly id="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_FROM) : "") ?>" size="3">
										<input type="hidden" name="SUBCAT_ID_<?= $arCatalogGroup["ID"] ?>[<?= $ind ?>]" value="<?= htmlspecialcharsbx($str_CAT_ID) ?>">
									</td>
									<td valign="top" align="center">
										<input type="text" disabled readonly id="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_TO) : "") ?>" size="3">

									</td>
									<td valign="top" align="center">
										<?
										echo CExtra::SelectBox("SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeSubExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
										?>

									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="10" OnChange="ptSubPriceChangeEx(this)">

									</td>
									<td valign="top" align="center">

											<?= CCurrency::SelectBox("SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeSubCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>
											<script type="text/javascript">
												jsUtils.addEvent(window, 'load', function() {ChangeSubExtraEx(BX('SUBCAT_EXTRA_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>'));});
											</script>

									</td>
								</tr>
								<?
							}

							if ($bVarsFromForm && $ind < intval(${"SUBCAT_ROW_COUNTER_".$arCatalogGroup["ID"]}))
							{
								for ($i = $ind + 1; $i <= intval(${"SUBCAT_ROW_COUNTER_".$arCatalogGroup["ID"]}); $i++)
								{
									$boolExistPrice = false;
									$ind++;
									$str_CAT_QUANTITY_FROM = ${"SUBCAT_BASE_QUANTITY_FROM_".$ind};
									$str_CAT_QUANTITY_TO = ${"SUBCAT_BASE_QUANTITY_TO_".$ind};
									$str_CAT_EXTRA = ${"SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind};
									$str_CAT_PRICE = ${"SUBCAT_PRICE_".$arCatalogGroup["ID"]."_".$ind};
									$str_CAT_CURRENCY = ${"SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind};
									if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
										$boolExistPrice = true;
									$arCatPricesExist[$ind][$arCatalogGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
									?>
									<tr id="submodel3_<?= $arCatalogGroup["ID"] ?>">
										<td valign="top" align="center">
											<input type="text" disabled readonly id="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_FROM) : "") ?>" size="3">
											<input type="hidden" name="SUBCAT_ID_<?= $arCatalogGroup["ID"] ?>[<?= $ind ?>]" value="<?= 0 ?>">
										</td>
										<td valign="top" align="center">
											<input type="text" disabled readonly id="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_TO) : "") ?>" size="3">

										</td>
										<td valign="top" align="center">
											<?
											echo CExtra::SelectBox("SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeSubExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
											?>

										</td>
										<td valign="top" align="center">
											<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="10" OnChange="ptSubPriceChangeEx(this)">

										</td>
										<td valign="top" align="center">

												<?= CCurrency::SelectBox("SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeSubCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>
												<script type="text/javascript">
													jsUtils.addEvent(window, 'load', function () {ChangeSubExtraEx(BX('SUBCAT_EXTRA_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>'));});
												</script>

										</td>
									</tr>
									<?
								}
							}
							if ($ind == -1)
							{
								$ind++;
								?>
								<tr id="submodel3_<?= $arCatalogGroup["ID"] ?>">
									<td valign="top" align="center">
										<input type="text" disabled readonly id="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="3">
									</td>
									<td valign="top" align="center">
										<input type="text" disabled readonly id="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="3">

									</td>
									<td valign="top" align="center">
										<?
										echo CExtra::SelectBox("SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, "", GetMessage("VAL_NOT_SET"), "ChangeSubExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
										?>

									</td>
									<td valign="top" align="center">
										<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="SUBCAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="10" OnChange="ptSubPriceChangeEx(this)">

									</td>
									<td valign="top" align="center">

											<?= CCurrency::SelectBox("SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, "", GetMessage("VAL_BASE"), false, "ChangeSubCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."SUBCAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>

									</td>
								</tr>
								<?
								$arCatPricesExist[$ind][$arCatalogGroup['ID']] = 'N';
							}
							?>
							</tbody>
						</table>
						<input type="hidden" name="SUBCAT_ROW_COUNTER_<?= $arCatalogGroup["ID"] ?>" id="SUBCAT_ROW_COUNTER_<?= $arCatalogGroup["ID"] ?>" value="<?= $ind ?>">
					</td>
				</tr>
				<?
			}
			?>
		</table>
		<div id="ext_subprice_exist">
		<?
		foreach ($arCatPricesExist as $ind => $arPriceExist)
		{
			$strExist = (in_array('Y',$arPriceExist) ? 'Y' : 'N');
			?>
			<input type="hidden" name="SUBCAT_PRICE_EXIST_<? echo $ind; ?>" id="SUBCAT_PRICE_EXIST_<? echo $ind; ?>" value="<? echo $strExist; ?>"><?
		}
		?>
		</div>
</div>
		<?
		$subtabControl1->BeginNextTab();
		?>

		<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="subcatalog_properties_table">
			<tr>
				<td width="40%">
					<?echo GetMessage("BASE_QUANTITY")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_QUANTITY = $arBaseProduct["QUANTITY"];
					if ($bVarsFromForm) $str_CAT_BASE_QUANTITY = $SUBCAT_BASE_QUANTITY;
					?>
					<input type="text" name="SUBCAT_BASE_QUANTITY" <?if ($bReadOnly) echo "disabled readonly" ?> value="<?echo htmlspecialcharsbx($str_CAT_BASE_QUANTITY) ?>" size="30">

				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("BASE_TRACE")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_QUANTITY_TRACE = $arBaseProduct["QUANTITY_TRACE_ORIG"];
					if ($bVarsFromForm) $str_CAT_BASE_QUANTITY_TRACE = $SUBCAT_BASE_QUANTITY_TRACE;
					$availQuantityTrace = COption::GetOptionString("catalog", "default_quantity_trace", 'N');
					?>
					<select id="SUBCAT_BASE_QUANTITY_TRACE" name="SUBCAT_BASE_QUANTITY_TRACE" <?if ($bReadOnly) echo "disabled readonly" ?>>
						<option value="D" <?if ("D"==$str_CAT_BASE_QUANTITY_TRACE) echo " selected"?>><?=GetMessage("C2IT_DEFAULT_NEGATIVE")." ("?><?echo $availQuantityTrace=='Y' ? GetMessage("C2IT_YES_NEGATIVE") : GetMessage("C2IT_NO_NEGATIVE")?>) </option>
						<option value="Y" <?if ("Y"==$str_CAT_BASE_QUANTITY_TRACE) echo " selected"?>><?=GetMessage("C2IT_YES_NEGATIVE")?></option>
						<option value="N" <?if ("N"==$str_CAT_BASE_QUANTITY_TRACE) echo " selected"?>><?=GetMessage("C2IT_NO_NEGATIVE")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("C2IT_CAN_BUY_NULL")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_CAN_BUY_ZERO = $arBaseProduct["CAN_BUY_ZERO_ORIG"];
					if ($bVarsFromForm) $str_CAT_BASE_CAN_BUY_ZERO = $SUBUSE_STORE;
					$availCanBuyZero = COption::GetOptionString("catalog", "default_can_buy_zero", 'N');
					?>
					<select id="SUBUSE_STORE" name="SUBUSE_STORE" <?if ($bReadOnly) echo "disabled readonly" ?>>
						<option value="D" <?if ("D"==$str_CAT_BASE_CAN_BUY_ZERO) echo " selected"?>><?=GetMessage("C2IT_DEFAULT_NEGATIVE")." ("?><?echo $availCanBuyZero=='Y' ? GetMessage("C2IT_YES_NEGATIVE") : GetMessage("C2IT_NO_NEGATIVE")?>) </option>
						<option value="Y" <?if ("Y"==$str_CAT_BASE_CAN_BUY_ZERO) echo " selected"?>><?=GetMessage("C2IT_YES_NEGATIVE")?></option>
						<option value="N" <?if ("N"==$str_CAT_BASE_CAN_BUY_ZERO) echo " selected"?>><?=GetMessage("C2IT_NO_NEGATIVE")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("C2IT_CAN_NEGATIVE_AMOUMT")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE = $arBaseProduct["NEGATIVE_AMOUNT_TRACE_ORIG"];
					if ($bVarsFromForm) $str_CAT_BASE_NEGATIVE_AMOUNT_TRACE = $SUBNEGATIVE_AMOUNT;
					$availNegativeAmountGlobal = COption::GetOptionString("catalog", "allow_negative_amount", 'N');
					?>
					<select id="SUBNEGATIVE_AMOUNT" name="SUBNEGATIVE_AMOUNT" <?if ($bReadOnly) echo "disabled readonly" ?>>
						<option value="D" <?if ("D"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_DEFAULT_NEGATIVE")." ("?><?echo $availNegativeAmountGlobal=='Y' ? GetMessage("C2IT_YES_NEGATIVE") : GetMessage("C2IT_NO_NEGATIVE")?>) </option>
						<option value="Y" <?if ("Y"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_YES_NEGATIVE")?></option>
						<option value="N" <?if ("N"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_NO_NEGATIVE")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td>
					<?echo GetMessage("BASE_WEIGHT")?>:
				</td>
				<td>
					<?
					$str_CAT_BASE_WEIGHT = $arBaseProduct["WEIGHT"];
					if ($bVarsFromForm) $str_CAT_BASE_WEIGHT = $SUBCAT_BASE_WEIGHT;
					?>
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_BASE_WEIGHT" value="<?echo htmlspecialcharsbx($str_CAT_BASE_WEIGHT) ?>" size="30">

				</td>
			</tr>
			<?
			if ($arCatalog["SUBSCRIPTION"]=="Y")
			{
				?>
				<tr class="heading">
					<td colspan="2"><?echo GetMessage("C2IT_SUBSCR_PARAMS")?></td>
				</tr>
				<tr>
					<td>
						<?echo GetMessage("C2IT_PAY_TYPE")?>
					</td>
					<td>
						<script type="text/javascript">
						function ChangeSubPriceType()
						{
							if (bReadOnly)
								return;

							var e_pt = BX('SUBCAT_PRICE_TYPE');

							if (e_pt.options[e_pt.selectedIndex].value == "S")
							{
								BX('SUBCAT_RECUR_SCHEME_TYPE').disabled = true;
								BX('SUBCAT_RECUR_SCHEME_LENGTH').disabled = true;
								BX('SUBCAT_TRIAL_PRICE_ID').disabled = true;
								BX('SUBCAT_TRIAL_PRICE_ID_BUTTON').disabled = true;
							}
							else
							{
								if (e_pt.options[e_pt.selectedIndex].value == "R")
								{
									BX('SUBCAT_RECUR_SCHEME_TYPE').disabled = false;
									BX('SUBCAT_RECUR_SCHEME_LENGTH').disabled = false;
									BX('SUBCAT_TRIAL_PRICE_ID').disabled = true;
									BX('SUBCAT_TRIAL_PRICE_ID_BUTTON').disabled = true;
								}
								else
								{
									BX('SUBCAT_RECUR_SCHEME_TYPE').disabled = false;
									BX('SUBCAT_RECUR_SCHEME_LENGTH').disabled = false;
									BX('SUBCAT_TRIAL_PRICE_ID').disabled = false;
									BX('SUBCAT_TRIAL_PRICE_ID_BUTTON').disabled = false;
								}
							}
						}
						</script>

						<?
						$str_CAT_PRICE_TYPE = $arBaseProduct["PRICE_TYPE"];
						if ($bVarsFromForm) $str_CAT_PRICE_TYPE = $SUBCAT_PRICE_TYPE;
						?>
						<select id="SUBCAT_PRICE_TYPE" name="SUBCAT_PRICE_TYPE" OnChange="ChangeSubPriceType()">
							<option value="S"<?if ($str_CAT_PRICE_TYPE=="S") echo " selected";?>><?echo GetMessage("C2IT_SINGLE")?></option>
							<option value="R"<?if ($str_CAT_PRICE_TYPE=="R") echo " selected";?>><?echo GetMessage("C2IT_REGULAR")?></option>
							<option value="T"<?if ($str_CAT_PRICE_TYPE=="T") echo " selected";?>><?echo GetMessage("C2IT_TRIAL")?></option>
						</select>

					</td>
				</tr>
				<tr>
					<td>
						<?echo GetMessage("C2IT_PERIOD_LENGTH")?>
					</td>
					<td>

						<?
						$str_CAT_RECUR_SCHEME_LENGTH = $arBaseProduct["RECUR_SCHEME_LENGTH"];
						if ($bVarsFromForm) $str_CAT_RECUR_SCHEME_LENGTH = $SUBCAT_RECUR_SCHEME_LENGTH;
						?>
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="SUBCAT_RECUR_SCHEME_LENGTH" name="SUBCAT_RECUR_SCHEME_LENGTH" value="<?echo htmlspecialcharsbx($str_CAT_RECUR_SCHEME_LENGTH) ?>" size="10">

					</td>
				</tr>
				<tr>
					<td>
						<?echo GetMessage("C2IT_PERIOD_TIME")?>
					</td>
					<td>
						<?
						$str_CAT_RECUR_SCHEME_TYPE = $arBaseProduct["RECUR_SCHEME_TYPE"];
						if ($bVarsFromForm) $str_CAT_RECUR_SCHEME_TYPE = $SUBCAT_RECUR_SCHEME_TYPE;
						?>
						<select id="SUBCAT_RECUR_SCHEME_TYPE" name="SUBCAT_RECUR_SCHEME_TYPE">
							<?
							reset($CATALOG_TIME_PERIOD_TYPES);
							foreach ($CATALOG_TIME_PERIOD_TYPES as $key => $value)
							{
								?><option value="<?= $key ?>"<?if ($str_CAT_RECUR_SCHEME_TYPE==$key) echo " selected";?>><?= $value ?></option><?
							}
							?>
						</select>
					</td>
				</tr>
				<tr>
					<td>
						<?echo GetMessage("C2IT_TRIAL_FOR")?>
					</td>
					<td>

						<?
						$str_CAT_TRIAL_PRICE_ID = $arBaseProduct["TRIAL_PRICE_ID"];
						if ($bVarsFromForm) $str_CAT_TRIAL_PRICE_ID = $SUBCAT_TRIAL_PRICE_ID;
						$catProductName = "";
						if (intval($str_CAT_TRIAL_PRICE_ID) > 0)
						{
							$dbCatElement = CIBlockElement::GetByID(intval($str_CAT_TRIAL_PRICE_ID));
							if ($arCatElement = $dbCatElement->GetNext())
								$catProductName = $arCatElement["NAME"];
						}
						?>
						<input id="SUBCAT_TRIAL_PRICE_ID" name="SUBCAT_TRIAL_PRICE_ID" value="<?= htmlspecialcharsbx($str_CAT_TRIAL_PRICE_ID) ?>" size="5" type="text"><input type="button" id="SUBCAT_TRIAL_PRICE_ID_BUTTON" name="SUBCAT_TRIAL_PRICE_ID_BUTTON" value="..." onClick="window.open('cat_product_search.php?IBLOCK_ID=<?= $IBLOCK_ID ?>&amp;field_name=SUBCAT_TRIAL_PRICE_ID&amp;alt_name=subtrial_price_alt&amp;form_name='+getElementSubFormName(), '', 'scrollbars=yes,resizable=yes,width=600,height=500,top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 600)/2-5));">&nbsp;<span id="subtrial_price_alt"><?= $catProductName ?></span>

					</td>
				</tr>
				<tr>
					<td>
						<?echo GetMessage("C2IT_WITHOUT_ORDER")?>
					</td>
					<td>
						<?
						$str_CAT_WITHOUT_ORDER = $arBaseProduct["WITHOUT_ORDER"];
						if ($bVarsFromForm) $str_CAT_WITHOUT_ORDER = $SUBCAT_WITHOUT_ORDER;
						?>
						<input type="checkbox" <?if ($bReadOnly) echo "disabled readonly" ?> name="SUBCAT_WITHOUT_ORDER" value="Y" <?if ($str_CAT_WITHOUT_ORDER=="Y") echo "checked"?>>

					</td>
				</tr>
				<?
			}
			?>
		</table>
<script type="text/javascript">
SetSubFieldsStyle('subcatalog_properties_table');
</script>

		<?if ($arCatalog["SUBSCRIPTION"]=="Y"):?>
			<script type="text/javascript">
			ChangeSubPriceType();
			</script>
		<?endif;?>

		<?if ($arCatalog["SUBSCRIPTION"]=="Y"):?>

			<?
			$subtabControl1->BeginNextTab();
			?>

			<script type="text/javascript">
			function SubCatGroupsActivate(obj, id)
			{
				if (bReadOnly)
					return;

				var ed = BX('SUBCAT_ACCESS_LENGTH_' + id);
				var ed1 = BX('SUBCAT_ACCESS_LENGTH_TYPE_' + id);
				ed.disabled = !obj.checked;
				ed1.disabled = !obj.checked;
			}
			</script>
			<table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
				<tr class="heading">
					<td><?echo GetMessage("C2IT_VKL")?></td>
					<td><?echo GetMessage("C2IT_USERS_GROUP")?></td>
					<td><?echo GetMessage("C2IT_ACTIVE_TIME")?> <sup>1)</sup></td>
				</tr>
				<?
				$arCurProductGroups = array();

				$dbProductGroups = CCatalogProductGroups::GetList(
						array(),
						array("PRODUCT_ID" => $ID),
						false,
						false,
						array("ID", "GROUP_ID", "ACCESS_LENGTH", "ACCESS_LENGTH_TYPE")
					);
				while ($arProductGroup = $dbProductGroups->Fetch())
				{
					$arCurProductGroups[intval($arProductGroup["GROUP_ID"])] = array(intval($arProductGroup["ACCESS_LENGTH"]), $arProductGroup["ACCESS_LENGTH_TYPE"]);
				}

				$arAvailContentGroups = array();
				$availContentGroups = COption::GetOptionString("catalog", "avail_content_groups");
				if (strlen($availContentGroups) > 0)
					$arAvailContentGroups = explode(",", $availContentGroups);

				$bNoAvailGroups = true;

				$dbGroups = CGroup::GetList(
						($b="c_sort"),
						($o="asc"),
						array("ANONYMOUS" => "N")
					);
				while ($arGroup = $dbGroups->Fetch())
				{
					$arGroup["ID"] = intval($arGroup["ID"]);

					if ($arGroup["ID"] == 2
						|| !in_array($arGroup["ID"], $arAvailContentGroups))
						continue;

					if ($bVarsFromForm)
					{
						if (isset(${"SUBCAT_USER_GROUP_ID_".$arGroup["ID"]}) && ${"SUBCAT_USER_GROUP_ID_".$arGroup["ID"]} == "Y")
						{
							$arCurProductGroups[$arGroup["ID"]] = array(intval(${"SUBCAT_ACCESS_LENGTH_".$arGroup["ID"]}), ${"SUBCAT_ACCESS_LENGTH_TYPE_".$arGroup["ID"]});
						}
						elseif (array_key_exists($arGroup["ID"], $arCurProductGroups))
						{
							unset($arCurProductGroups[$arGroup["ID"]]);
						}
					}

					$bNoAvailGroups = false;
					?>
					<tr>
						<td align="center">

								<input type="checkbox" name="SUBCAT_USER_GROUP_ID_<?= $arGroup["ID"] ?>" value="Y"<?if (array_key_exists($arGroup["ID"], $arCurProductGroups)) echo " checked";?> onclick="SubCatGroupsActivate(this, <?= $arGroup["ID"] ?>)">

						</td>
						<td align="left"><? echo htmlspecialcharsbx($arGroup["NAME"]); ?></td>
						<td align="center">

								<input type="text" id="SUBCAT_ACCESS_LENGTH_<?= $arGroup["ID"] ?>" name="SUBCAT_ACCESS_LENGTH_<?= $arGroup["ID"] ?>" size="5" <?
								if (array_key_exists($arGroup["ID"], $arCurProductGroups))
									echo 'value="'.$arCurProductGroups[$arGroup["ID"]][0].'" ';
								else
									echo 'disabled ';
								?>>
								<select id="SUBCAT_ACCESS_LENGTH_TYPE_<?= $arGroup["ID"] ?>" name="SUBCAT_ACCESS_LENGTH_TYPE_<?= $arGroup["ID"] ?>"<?
								if (!array_key_exists($arGroup["ID"], $arCurProductGroups))
									echo ' disabled';
								?>>
									<?
									reset($CATALOG_TIME_PERIOD_TYPES);
									foreach ($CATALOG_TIME_PERIOD_TYPES as $key => $value)
									{
										?><option value="<?= $key ?>"<?if ($arCurProductGroups[$arGroup["ID"]][1] == $key) echo " selected";?>><?= $value ?></option><?
									}
									?>
								</select>

						</td>
					</tr>
					<?
				}

				if ($bNoAvailGroups)
				{
					?>
					<tr>
						<td colspan="3"><? echo GetMessage("C2IT_NO_USER_GROUPS1")?> <a href="/bitrix/admin/settings.php?mid=catalog&lang=<? echo LANGUAGE_ID; ?>"><?echo GetMessage("C2IT_NO_USER_GROUPS2")?></a>.</td>
					</tr>
					<?
				}
				?>
			</table>
			<br><b>1)</b> <?echo GetMessage("C2IT_ZERO_HINT")?>
		<?endif;?>
		<?
		if(CBXFeatures::IsFeatureEnabled('CatMultiStore'))
		{
			$subtabControl1->BeginNextTab();
			?>
			<table border="0" cellspacing="0" cellpadding="0" class="internal" align="center">
				<tr class="heading">
					<td><?echo GetMessage("C2IT_STORE_NUMBER"); ?></td>
					<td><?echo GetMessage("C2IT_NAME"); ?></td>
					<td><?echo GetMessage("C2IT_STORE_ADDR"); ?></td>
					<td><?echo GetMessage("C2IT_PROD_AMOUNT"); ?></td>
				</tr>
			<?
			$arPropList = array();
			$arSelect = array(
				"ID",
				"TITLE",
				"ADDRESS",
				"PRODUCT_AMOUNT",
			);

			$rsProps = CCatalogStore::GetList(array('ID' => 'ASC'),array("PRODUCT_ID" => $PRODUCT_ID, 'ACTIVE' => 'Y'),false,false,$arSelect);
			$numStore = 1;
			while ($arProp = $rsProps->GetNext())
			{
				$amount = (is_null($arProp["PRODUCT_AMOUNT"])) ? 0 : $arProp["PRODUCT_AMOUNT"];
				$address = ($arProp['ADDRESS'] != '') ? $arProp['ADDRESS'] : '<a href="/bitrix/admin/cat_store_edit.php?ID='.$arProp['ID'].'&lang='.LANGUAGE_ID.'">'.GetMessage("C2IT_EDIT").'</a>';
				?>
				<tr>
					<td style="text-align:center;"><a href="/bitrix/admin/cat_store_edit.php?ID=<?=$arProp['ID']?>&lang=<? echo LANGUAGE_ID; ?>"><?=$numStore?></a></td>
					<td style="text-align:center;"><?=$arProp['TITLE']?></td>
					<td style="text-align:center;"><?=$address?></td>
					<td style="text-align:center;"><input type="text" id="SUBAR_AMOUNT" name="SUBAR_AMOUNT[<?=$arProp['ID']?>]" size="12" value="<?=$amount?>" /></td>
					<input type="hidden" name="SUBAR_STORE_ID[<?=$arProp['ID']?>]" value="<?=$arProp['ID']?>" />
				</tr>
				<?
				$numStore++;
			}
			?>
			</table>
			<?
			if($numStore < 2)
				echo "<b>".GetMessage("C2IT_STORE_NO_STORE")." \"<a href=\"/bitrix/admin/cat_store_list.php\">".GetMessage("C2IT_STORE")."</a>\".</b> <br>";
			echo "<br>".GetMessage("C2IT_STORE_HINT");
		}

		$subtabControl1->BeginNextTab();

		$arSectionsChain = array();
		if (is_array($str_IBLOCK_ELEMENT_SECTION))
		{
			foreach ($str_IBLOCK_ELEMENT_SECTION as $key => $SECT_ID)
			{
				$dbRes = CIBlockSection::GetNavChain($IBLOCK_ID, $SECT_ID);
				while ($arRes = $dbRes->Fetch())
				{
					$arSectionsChain[] = $arRes['ID'];
				}
			}
			$arSectionsChain = array_values(array_unique($arSectionsChain));
		}

		$arCatalogSiteList = array();
		$rsIBlockSites = CIBlock::GetSite($IBLOCK_ID);
		while ($arIBlockSite = $rsIBlockSites->Fetch())
		{
			$arCatalogSiteList[] = $arIBlockSite['SITE_ID'];
		}

		CCatalogDiscountSave::Disable();
		$arDiscountList = array();
		$dbProductDiscounts = CCatalogDiscount::GetList(
				array("ID" => "ASC"),
				array(
						"+PRODUCT_ID" => $PRODUCT_ID,
						"+SECTION_LIST" => $arSectionsChain,
						"+IBLOCK_ID" => $IBLOCK_ID,
						"SITE_ID" => $arCatalogSiteList,
						"ACTIVE" => "Y",
						"!>ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
						"!<ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
						"COUPON" => ""
					),
				false,
				false,
				array(
						"ID", "SITE_ID", "SORT", "NAME", "VALUE_TYPE", "VALUE", "CURRENCY"
					)
			);
		while ($arProductDiscounts = $dbProductDiscounts->Fetch())
		{
			$arDiscountList[$arProductDiscounts['ID']] = $arProductDiscounts;
		}
		if ($PRODUCT_ID > 0)
		{
			$arSKU = CCatalogSKU::GetProductInfo($PRODUCT_ID,$IBLOCK_ID);
		}
		else
		{
			$arSKU = false;
			$arSKULink = CCatalogSKU::GetInfoByOfferIBlock($IBLOCK_ID);
			if (!empty($arSKULink))
			{
				$arSKU = array(
					'ID' => 0,
					'IBLOCK_ID' => $arSKULink['PRODUCT_IBLOCK_ID'],
				);
			}
		}
		if (is_array($arSKU))
		{
			$arSKU['SECTIONS'] = array();
			if ($arSKU['ID'] > 0)
			{
				$rsSKUSections = CIBlockElement::GetElementGroups($arSKU['ID'],true);
				while ($arSKUSection = $rsSKUSections->Fetch())
				{
					$dbRes = CIBlockSection::GetNavChain($arSKU['IBLOCK_ID'], $arSKUSection['ID']);
					while ($arRes = $dbRes->Fetch())
					{
						$arSKU['SECTIONS'][] = $arRes['ID'];
					}
				}
				$arSKU['SECTIONS'] = array_values(array_unique($arSKU['SECTIONS']));
			}

			$dbProductDiscounts = CCatalogDiscount::GetList(
				array("ID" => "ASC"),
				array(
						"+PRODUCT_ID" => $arSKU['ID'],
						"+SECTION_LIST" => $arSKU['SECTIONS'],
						"+IBLOCK_ID" => $arSKU['IBLOCK_ID'],
						"SITE_ID" => $arCatalogSiteList,
						"ACTIVE" => "Y",
						"!>ACTIVE_FROM" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
						"!<ACTIVE_TO" => $DB->FormatDate(date("Y-m-d H:i:s"), "YYYY-MM-DD HH:MI:SS", CSite::GetDateFormat("FULL")),
						"COUPON" => ""
					),
				false,
				false,
				array(
						"ID", "SITE_ID", "SORT", "NAME", "VALUE_TYPE", "VALUE", "CURRENCY"
					)
			);
			while ($arProductDiscounts = $dbProductDiscounts->Fetch())
			{
				$arDiscountList[$arProductDiscounts['ID']] = $arProductDiscounts;
			}
			ksort($arDiscountList);
		}
		CCatalogDiscountSave::Enable();

		if (empty($arDiscountList))
		{
			?><b><?echo GetMessage("C2IT_NO_ACTIVE_DISCOUNTS")?></b><br><?
		}
		else
		{
			?><table border="0" cellspacing="0" cellpadding="0" class="internal" align="center" width="100%">
				<tr class="heading">
					<td>ID</td>
					<td><?echo GetMessage("C2IT_SITE")?></td>
					<td><?echo GetMessage("C2IT_ACTIVITY")?></td>
					<td><?echo GetMessage("C2IT_NAME")?></td>
					<td><?echo GetMessage("C2IT_AMOUNT")?></td>
					<? if ($bDiscount)
					{
					?><td><?echo GetMessage("C2IT_ACTIONS")?></td><?
					}
					?>
				</tr><?
			foreach ($arDiscountList as $arProductDiscounts)
			{
					$boolWork = true;
					if (in_array($arProductDiscounts["SITE_ID"],$arCatalogSiteList) == false)
						$boolWork = false;
					$strDiscountStyle = ($boolWork ? '' : ' style="color: #afafaf; font-style: italic;"')
				?><tr>
						<td align="center"<? echo $strDiscountStyle;?>><? echo $arProductDiscounts["ID"] ?></td>
						<td align="center"<? echo $strDiscountStyle;?>><? echo $arProductDiscounts["SITE_ID"] ?></td>
						<td align="center"<? echo $strDiscountStyle;?>><? echo GetMessage("C2IT_YES")?></td>
						<td align="left"<? echo $strDiscountStyle;?>><? echo htmlspecialcharsbx($arProductDiscounts["NAME"]) ?></td>
						<td align="right"<? echo $strDiscountStyle;?>>
							<?
							if ($arProductDiscounts["VALUE_TYPE"]=="P")
							{
								echo $arProductDiscounts["VALUE"]."%";
							}
							elseif ($arProductDiscounts["VALUE_TYPE"]=="S")
							{
								?>= <? echo FormatCurrency($arProductDiscounts["VALUE"], $arProductDiscounts["CURRENCY"]);
							}
							else
							{
								echo FormatCurrency($arProductDiscounts["VALUE"], $arProductDiscounts["CURRENCY"]);
							}
							?>
						</td>
						<?
						if ($bDiscount)
						{
						?>
						<td align="left">
							<a href="/bitrix/admin/cat_discount_edit.php?ID=<? echo $arProductDiscounts["ID"] ?>&lang=<? echo urlencode(LANGUAGE_ID); ?>#tb" target="_blank"><?echo GetMessage("C2IT_MODIFY")?></a>
						</td>
						<?
						}
						?>
					</tr>
					<?
			}
			?></table><?
		}
		?>
		<br>
		<?echo GetMessage("C2IT_DISCOUNT_HINT")?>
		<?
		$subtabControl1->End();
		?>
	</td>
</tr>
<?
}
?>