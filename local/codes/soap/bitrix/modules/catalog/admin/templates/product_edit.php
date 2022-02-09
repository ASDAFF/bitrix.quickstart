<?
if ($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_price'))
{
	include(GetLangFileName($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/lang/", "/templates/product_edit.php"));

	$IBLOCK_ID = intval($IBLOCK_ID);
	if ($IBLOCK_ID <= 0)
		return;
	$MENU_SECTION_ID = intval($MENU_SECTION_ID);
	$arCatalog = CCatalog::GetByID($IBLOCK_ID);
	$PRODUCT_ID = (0 < $ID ? CIBlockElement::GetRealElement($ID) : 0);
	$arBaseProduct = CCatalogProduct::GetByID($PRODUCT_ID);
	if (0 < $PRODUCT_ID)
	{
		$bReadOnly = !($USER->CanDoOperation('catalog_price') && CIBlockElementRights::UserHasRightTo($IBLOCK_ID, $PRODUCT_ID, "element_edit_price"));
	}
	else
	{
		$bReadOnly = !($USER->CanDoOperation('catalog_price') && CIBlockSectionRights::UserHasRightTo($IBLOCK_ID, $MENU_SECTION_ID, "element_edit_price"));
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
			function getElementForm()
			{
				for(var i = 0; i < document.forms.length; i++)
				{
					var check = document.forms[i].name.substring(0, 10).toUpperCase();
					if(check == 'FORM_ELEME' || check == 'TABCONTROL')
						return document.forms[i];
				}
			}
			function getElementFormName()
			{
				var form = getElementForm();
				if (form)
					return form.name;
				else
					return '';
			}
			function checkForm(e)
			{
				if (window.BX_CANCEL)
					return true;

				if (!e)
					e = window.event;

				var bReturn = true;

				if (document.getElementById('CAT_ROW_COUNTER').value > 0 && !!document.getElementById('price_useextform') && !document.getElementById('price_useextform').checked)
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
				var obForm = getElementForm();
				jsUtils.addEvent(obForm, 'submit', checkForm);
				jsUtils.addEvent(obForm.dontsave, 'click', function() {window.BX_CANCEL = true; setTimeout('window.BX_CANCEL = false', 10);});
			});
		</script>
	</td>
</tr>
<tr>
<td valign="top" colspan="2">
<script type="text/javascript">
	function SetFieldsStyle(table_id)
	{
		var tbl = document.getElementById(table_id);
		var n = tbl.rows.length;
		for(var i=0; i<n; i++)
			if(tbl.rows[i].cells[0].colSpan == 1)
				tbl.rows[i].cells[0].className = 'field-name';
	}
</script>
	<?
	$aTabs1 = array();
	$aTabs1[] = array("DIV" => "cat_edit1", "TAB" => GetMessage("C2IT_PRICES"), "TITLE" => GetMessage("C2IT_PRICES_D"));
	$aTabs1[] = array("DIV" => "cat_edit3", "TAB" => GetMessage("C2IT_PARAMS"), "TITLE" => GetMessage("C2IT_PARAMS_D"));
	if($arCatalog["SUBSCRIPTION"] == "Y")
		$aTabs1[] = array("DIV" => "cat_edit4", "TAB" => GetMessage("C2IT_GROUPS"), "TITLE" => GetMessage("C2IT_GROUPS_D"));
	if(CBXFeatures::IsFeatureEnabled('CatMultiStore'))
		$aTabs1[] = array("DIV" => "cat_edit5", "TAB" => GetMessage("C2IT_STORE"), "TITLE" => GetMessage("C2IT_STORE_D"));
	$aTabs1[] = array("DIV" => "cat_edit6", "TAB" => GetMessage("C2IT_DISCOUNTS"), "TITLE" => GetMessage("C2IT_DISCOUNTS_D"));

	$tabControl1 = new CAdminViewTabControl("tabControl1", $aTabs1);
	$tabControl1->Begin();

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
				for ($i = 0, $intCount = count($arPriceBoundaries); $i < $intCount; $i++)
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

	for ($i = 0, $intCount = count($arPriceBoundaries); $i < $intCount - 1; $i++)
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
	function togglePriceType()
	{
		var obPriceSimple = BX('prices_simple');
		var obPriceExt = BX('prices_ext');
		var obBasePrice = BX('tr_BASE_PRICE');
		var obBaseCurrency = BX('tr_BASE_CURRENCY');

		if (obPriceSimple.style.display == 'block')
		{
			obPriceSimple.style.display = 'none';
			obPriceExt.style.display = 'block';
			if (!!obBasePrice)
				BX.style(obBasePrice, 'display', 'none');
			if (!!obBaseCurrency)
				BX.style(obBaseCurrency, 'display', 'none');
		}
		else
		{
			obPriceSimple.style.display = 'block';
			obPriceExt.style.display = 'none';
			if (!!obBasePrice)
				BX.style(obBasePrice, 'display', 'table-row');
			if (!!obBaseCurrency)
				BX.style(obBaseCurrency, 'display', 'table-row');
		}
	}
</script>
	<?
// prices tab
	$tabControl1->BeginNextTab();
	$arCatPricesExist = array(); // attr for exist prices for range
	$bUseExtendedPrice = $bVarsFromForm ? $price_useextform == 'Y' : count($arPriceBoundaries) > 1;
	$str_CAT_VAT_ID = $bVarsFromForm ? $CAT_VAT_ID : ($arBaseProduct['VAT_ID'] == 0 ? $arCatalog['VAT_ID'] : $arBaseProduct['VAT_ID']);
	$str_CAT_VAT_INCLUDED = $bVarsFromForm ? $CAT_VAT_INCLUDED : $arBaseProduct['VAT_INCLUDED'];
	?>
<input type="hidden" name="price_useextform" id="price_useextform_N" value="N" />
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="catalog_vat_table">
<?
if (CBXFeatures::IsFeatureEnabled('CatMultiPrice'))
{
?>
	<tr>
		<td width="40%"><label for="price_useextform"><? echo GetMessage('C2IT_PRICES_USEEXT'); ?>:</label></td>
		<td width="60%">
			<input type="checkbox" name="price_useextform" id="price_useextform" value="Y" onclick="togglePriceType()" <?=$bUseExtendedPrice ? 'checked="checked"' : ''?> <? echo ((!CBXFeatures::IsFeatureEnabled('CatMultiPrice') || $bReadOnly) ? ' disabled readonly' : ''); ?>/>
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
			echo SelectBoxFromArray('CAT_VAT_ID', $arVATRef, $str_CAT_VAT_ID, "", $bReadOnly ? "disabled readonly" : '');
			?>
		</td>
	</tr>
	<tr>
		<td width="40%"><label for="CAT_VAT_INCLUDED"><?echo GetMessage("CAT_VAT_INCLUDED")?></label>:</td>
		<td width="60%">
			<input type="hidden" name="CAT_VAT_INCLUDED" id="CAT_VAT_INCLUDED_N" value="N">
			<input type="checkbox" name="CAT_VAT_INCLUDED" id="CAT_VAT_INCLUDED" value="Y" <?=$str_CAT_VAT_INCLUDED == 'Y' ? 'checked="checked"' : ''?> <?=$bReadOnly ? 'disabled readonly' : ''?> />
		</td>
	</tr>
	<tr id="tr_BASE_PRICE" style="display: <? echo ($bUseExtendedPrice ? 'none' : 'table-row'); ?>;">
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
function OnChangeExtra(priceType)
{
	if (bReadOnly)
		return;
	var e_base_price = BX('CAT_BASE_PRICE');
	var e_extra = BX('CAT_EXTRA_' + priceType);
	var e_price = BX('CAT_PRICE_' + priceType);
	var e_currency = BX('CAT_CURRENCY_' + priceType);

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

function OnChangeExtraEx(e)
{
	if (bReadOnly)
		return;

	var thename = e.name;

	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);
	thename = thename.substr(0, pos);
	pos = thename.lastIndexOf("_");
	var ptype = thename.substr(pos + 1);

	var e_ext = BX('CAT_EXTRA_'+ptype+"_"+ind);
	var e_price = BX('CAT_PRICE_'+ptype+"_"+ind);
	var e_currency = BX('CAT_CURRENCY_'+ptype+"_"+ind);

	var e_base_price = BX('CAT_BASE_PRICE_'+ind);

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

function ChangeExtra(codID)
{
	if (bReadOnly)
		return;

	OnChangeExtra(codID);

	var e_extra = BX('CAT_EXTRA_' + codID + '_0');
	if (e_extra)
	{
		var e_extra_s = document.getElementById('CAT_EXTRA_' + codID);
		e_extra.selectedIndex = e_extra_s.selectedIndex;
		OnChangeExtraEx(e_extra);
	}
}

function OnChangeBasePrice()
{
	if (bReadOnly)
		return;

	var e_base_price = document.getElementById('CAT_BASE_PRICE');

	if (isNaN(e_base_price.value) || e_base_price.value <= 0)
	{
		var k;
		for (k = 0; k < arCatalogGroups.length; k++)
		{
			e_price = BX('CAT_PRICE_' + arCatalogGroups[k]);
			e_price.disabled = false;
			e_currency = BX('CAT_CURRENCY_' + arCatalogGroups[k]);
			e_currency.disabled = false;
		}
		OnChangePriceExist();
		return;
	}

	var i, j, esum, eps;
	var e_price;
	for (i = 0; i < arCatalogGroups.length; i++)
	{
		e_extra = document.getElementById('CAT_EXTRA_' + arCatalogGroups[i]);
		if (e_extra.selectedIndex > 0)
		{
			e_price = document.getElementById('CAT_PRICE_' + arCatalogGroups[i]);
			e_currency = BX('CAT_CURRENCY_' + arCatalogGroups[i]);

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
	OnChangePriceExist();
}

function ChangeBasePrice(e)
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

	OnChangeBasePrice();

	var e_base_price = BX('CAT_BASE_PRICE_0');
	e_base_price.value = BX('CAT_BASE_PRICE').value;
	OnChangeBasePriceEx(e_base_price);
	OnChangePriceExistEx(e_base_price);
}

function ChangeBaseCurrency()
{
	if (bReadOnly)
		return;

	document.getElementById('CAT_BASE_CURRENCY_0').selectedIndex = document.getElementById('CAT_BASE_CURRENCY').selectedIndex;
}

function ChangePrice(codID)
{
	if (bReadOnly)
		return;

	var e_price = document.getElementById('CAT_PRICE_' + codID + '_0');
	e_price.value = document.getElementById('CAT_PRICE_' + codID).value;
	OnChangePriceExist();
	OnChangePriceExistEx(e_price);
}

function ChangeCurrency(codID)
{
	if (bReadOnly)
		return;

	var e_currency = document.getElementById('CAT_CURRENCY_' + codID + "_0");
	e_currency.selectedIndex = document.getElementById('CAT_CURRENCY_' + codID).selectedIndex;
}

function OnChangePriceExist()
{
	if (bReadOnly)
		return;

	var bExist = 'N';
	var e_price_exist = BX('CAT_PRICE_EXIST');
	var e_ext_price_exist = BX('CAT_PRICE_EXIST_0');
	var e_base_price = BX('CAT_BASE_PRICE');

	if (isNaN(e_base_price.value) || e_base_price.value <= 0)
	{
		var i;
		var e_price;
		for (i = 0; i < arCatalogGroups.length; i++)
		{
			e_price = BX('CAT_PRICE_' + arCatalogGroups[i]);
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
		$str_CAT_BASE_PRICE = $CAT_BASE_PRICE;
	if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
		$boolBaseExistPrice = true;
	?>
			<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_PRICE" name="CAT_BASE_PRICE" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="30" OnBlur="ChangeBasePrice(this)">
		</td>
	</tr>
	<tr id="tr_BASE_CURRENCY" style="display: <? echo ($bUseExtendedPrice ? 'none' : 'table-row'); ?>;">
		<td width="40%">
			<?echo GetMessage("BASE_CURRENCY")?>:
		</td>
		<td width="60%">
		<?
		if ($arBasePrice)
			$str_CAT_BASE_CURRENCY = $arBasePrice["CURRENCY"];
		if ($bVarsFromForm)
			$str_CAT_BASE_CURRENCY = $CAT_BASE_CURRENCY;

		$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));
		?>
			<select id="CAT_BASE_CURRENCY" name="CAT_BASE_CURRENCY" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeBaseCurrency()">
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
	SetFieldsStyle('catalog_vat_table');
</script>

	<?
// simple price form
	?>
<div id="prices_simple" style="display: <?=$bUseExtendedPrice ? 'none' : 'block'?>;">
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
				$str_CAT_EXTRA = ${"CAT_EXTRA_".$arCatalogGroup["ID"]};
				$str_CAT_PRICE = ${"CAT_PRICE_".$arCatalogGroup["ID"]};
				$str_CAT_CURRENCY = ${"CAT_CURRENCY_".$arCatalogGroup["ID"]};
			}
			if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
				$boolBaseExistPrice = true;
			?>
			<tr <?if ($bReadOnly) echo "disabled readonly" ?>>
				<td valign="top" align="left">
					<? echo htmlspecialcharsbx(!empty($arCatalogGroup['NAME_LANG']) ? $arCatalogGroup['NAME_LANG'] : $arCatalogGroup["NAME"]); ?>
					<?if ($arPrice):?>
					<input type="hidden" name="CAT_ID_<?echo $arCatalogGroup["ID"] ?>" value="<?echo $arPrice["ID"] ?>">
					<?endif;?>
				</td>
				<td valign="top" align="center">
					<?
					echo CExtra::SelectBox("CAT_EXTRA_".$arCatalogGroup["ID"], $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeExtra(".$arCatalogGroup["ID"].")", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_EXTRA_".$arCatalogGroup["ID"].'" ');
					?>
				</td>
				<td valign="top" align="center">
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_PRICE_<?echo $arCatalogGroup["ID"] ?>" name="CAT_PRICE_<?echo $arCatalogGroup["ID"] ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="8" OnChange="ChangePrice(<?= $arCatalogGroup["ID"] ?>)">
				</td>
				<td valign="top" align="center">
					<?
					echo CCurrency::SelectBox("CAT_CURRENCY_".$arCatalogGroup["ID"], $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeCurrency(".$arCatalogGroup["ID"].")", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_CURRENCY_".$arCatalogGroup["ID"].'" ')
					?>
					<script type="text/javascript">
						ChangeExtra(<?echo $arCatalogGroup["ID"] ?>);
					</script>
				</td>
			</tr>
			<?
		}// endwhile
		if(!$bFirst) echo "</table>";
	}
	?><input type="hidden" name="CAT_PRICE_EXIST" id="CAT_PRICE_EXIST" value="<? echo ($boolBaseExistPrice == true ? 'Y' : 'N'); ?>">
</div>
	<?
	//$tabControl1->BeginNextTab();
// extended price form
	?>
<div id="prices_ext" style="display: <?=$bUseExtendedPrice ? 'block' : 'none'?>;">
<script type="text/javascript">
function addNewElementsGroup(parentId, modelId, counterId, keepValues, typefocus)
{
	if (bReadOnly)
		return;

	if (!document.getElementById(counterId))
		return false;
	var n = ++document.getElementById(counterId).value;
	var thebody = document.getElementById(parentId);
	if (!thebody)
		return false;
	var therow = document.getElementById(modelId);
	if (!therow)
		return false;
	var thecopy = duplicateElement(therow, n, keepValues);
	thebody.appendChild(thecopy);

	return true;
}

function duplicateElement(e, n, keepVal)
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
					hijocopia = duplicateElement(hijos[key], n, keepVal);
					if (hijocopia) copia.appendChild(hijocopia);
				}
			}
		}
		return copia;
	}
	return null;
}

function CloneBasePriceGroup()
{
	if (bReadOnly)
		return;

	var oTbl = BX("BASE_PRICE_GROUP_TABLE");
	if (!oTbl)
		return;

	var oCntr = document.getElementById("CAT_ROW_COUNTER");
	var cnt = parseInt(oCntr.value);
	cnt = cnt + 1;

	var oRow = oTbl.insertRow(-1);
	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_FROM_'+cnt+'" value="" size="3" OnChange="ChangeBaseQuantityEx(this)">';

	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_TO_'+cnt+'" value="" size="3" OnChange="ChangeBaseQuantityEx(this)">';

	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_PRICE_'+cnt+'" name="CAT_BASE_PRICE_'+cnt+'" value="" size="15" OnBlur="ChangeBasePriceEx(this)">';

	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	var str = '';
	<? $dbCurrencyList = CCurrency::GetList(($by1="sort"), ($order1="asc")); ?>
	str = '<select id="CAT_BASE_CURRENCY_'+cnt+'" name="CAT_BASE_CURRENCY_'+cnt+'" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeBaseCurrencyEx(this)">';
	<?
	while ($arCurrencyList = $dbCurrencyList->Fetch())
	{
		?>str += '<option value="<?echo $arCurrencyList["CURRENCY"] ?>"><?echo $arCurrencyList["CURRENCY"]?> (<?echo CUtil::JSEscape(htmlspecialcharsbx($arCurrencyList["FULL_NAME"]))?>)</option>';<?
	}
	?>
	str += '</select>';
	oCell.innerHTML = str;

	var div_ext_price_exist = BX('ext_price_exist');
	var new_price_exist = BX.create('input',
		{'attrs': {
			'type': 'hidden',
			'name': 'CAT_PRICE_EXIST_'+cnt,
			'value': 'N'
		}
		});
	new_price_exist.id = 'CAT_PRICE_EXIST_'+cnt,
		div_ext_price_exist.appendChild(new_price_exist);
	oCntr.value = cnt;
}

function CloneOtherPriceGroup(ind)
{
	if (bReadOnly)
		return;

	var oTbl = document.getElementById("OTHER_PRICE_GROUP_TABLE_"+ind);
	if (!oTbl)
		return;

	var oCntr = document.getElementById("CAT_ROW_COUNTER_"+ind);
	var cnt = parseInt(oCntr.value);
	cnt = cnt + 1;

	var oRow = oTbl.insertRow(-1);
	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	oCell.innerHTML = '<input type="text" disabled readonly id="CAT_QUANTITY_FROM_'+ind+'_'+cnt+'" name="CAT_QUANTITY_FROM_'+ind+'_'+cnt+'" value="" size="3">';

	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	oCell.innerHTML = '<input type="text" disabled readonly id="CAT_QUANTITY_TO_'+ind+'_'+cnt+'" name="CAT_QUANTITY_TO_'+ind+'_'+cnt+'" value="" size="3">';

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
	str += '<select id="CAT_EXTRA_'+ind+'_'+cnt+'" name="CAT_EXTRA_'+ind+'_'+cnt+'" OnChange="ChangeExtraEx(this)" <?if ($bReadOnly) echo "disabled readonly" ?>>';
	str += '<option value=""><?= GetMessage("VAL_NOT_SET") ?></option>';
	<?
	for ($i = 0, $intCount = count($GLOBALS["MAIN_EXTRA_LIST_CACHE"]); $i < $intCount; $i++)
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
	oCell.innerHTML = '<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_PRICE_'+ind+'_'+cnt+'" name="CAT_PRICE_'+ind+'_'+cnt+'" value="" size="10" OnChange="ptPriceChangeEx(this)">';

	var oCell = oRow.insertCell(-1);
	oCell.valign = "top";
	oCell.align = "center";
	var str = '';
	<?$dbCurrencyList = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
	str += '<select id="CAT_CURRENCY_'+ind+'_'+cnt+'" name="CAT_CURRENCY_'+ind+'_'+cnt+'" OnChange="ChangeCurrencyEx(this)" <?if ($bReadOnly) echo "disabled readonly" ?>>';
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

function ClonePriceSections()
{
	if (bReadOnly)
		return;

	CloneBasePriceGroup();

	var i, n;
	for (i = 0; i < arCatalogGroups.length; i++)
	{
		CloneOtherPriceGroup(arCatalogGroups[i]);

		n = document.getElementById('CAT_ROW_COUNTER_'+arCatalogGroups[i]).value;
		ChangeExtraEx(document.getElementById('CAT_EXTRA_'+arCatalogGroups[i]+"_"+n));
	}
}

function ChangeBaseQuantityEx(e)
{
	if (bReadOnly)
		return;

	var thename = e.name;

	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);

	var type;
	if (thename.substring(0, "CAT_BASE_QUANTITY_FROM_".length) == "CAT_BASE_QUANTITY_FROM_")
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
		quantity = document.getElementById('CAT_QUANTITY_'+type+"_"+arCatalogGroups[i]+"_"+ind);
		quantity.value = e.value;
	}
}

function OnChangeBasePriceEx(e)
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
			e_price = document.getElementById('CAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
			e_price.disabled = false;
			e_cur = document.getElementById('CAT_CURRENCY_'+arCatalogGroups[i]+"_"+ind);
			e_cur.disabled = false;
		}
		OnChangePriceExistEx(e);
		return;
	}

	var i;
	var e_price, e_ext;

	for (i = 0; i < arCatalogGroups.length; i++)
	{
		e_price = BX('CAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
		e_cur = BX('CAT_CURRENCY_'+arCatalogGroups[i]+"_"+ind);
		e_ext = BX('CAT_EXTRA_'+arCatalogGroups[i]+"_"+ind);

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
	OnChangePriceExistEx(e);
}

function ChangeBasePriceEx(e)
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

	OnChangeBasePriceEx(e);

	var thename = e.name;
	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);

	if (parseInt(ind) == 0)
	{
		BX('CAT_BASE_PRICE').value = e.value;
		OnChangeBasePrice();
		OnChangePriceExist();
	}
}

function ChangeExtraEx(e)
{
	if (bReadOnly)
		return;

	if (null == e)
		return;

	OnChangeExtraEx(e);
	var thename = e.name;

	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);
	thename = thename.substr(0, pos);
	pos = thename.lastIndexOf("_");
	var ptype = thename.substr(pos + 1);

	if (parseInt(ind) == 0)
	{
		document.getElementById('CAT_EXTRA_'+ptype).selectedIndex = e.selectedIndex;
		OnChangeExtra(ptype);
	}
}

function ChangeBaseCurrencyEx(e)
{
	if (bReadOnly)
		return;

	var thename = e.name;

	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);

	if (parseInt(ind) == 0)
	{
		document.getElementById('CAT_BASE_CURRENCY').selectedIndex = e.selectedIndex;
	}
}

function ptPriceChangeEx(e)
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
		BX('CAT_PRICE_'+ptype).value = e.value;
		OnChangePriceExist();
	}
	OnChangePriceExistEx(e);
}

function ChangeCurrencyEx(e)
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

		document.getElementById('CAT_CURRENCY_'+ptype).selectedIndex = e.selectedIndex;
	}
}

function OnChangePriceExistEx(e)
{
	if (bReadOnly)
		return;

	var thename = e.name;

	var pos = thename.lastIndexOf("_");
	var ind = thename.substr(pos + 1);

	if (!(isNaN(ind) || parseInt(ind) < 0))
	{
		var price_ext = BX('CAT_PRICE_EXIST_'+ind);
		if (!price_ext)
			return;

		var i;
		var e_price;
		bExist = 'N';
		e_price = BX('CAT_BASE_PRICE_'+ind);
		if (!e_price)
			return;

		if (isNaN(e_price.value) || e_price.value <= 0)
		{
			for (i = 0; i < arCatalogGroups.length; i++)
			{
				e_price = document.getElementById('CAT_PRICE_'+arCatalogGroups[i]+"_"+ind);
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
		<table border="0" cellspacing="1" cellpadding="3" id="BASE_PRICE_GROUP_TABLE">
			<thead>
			<tr>
				<td align="center"><?echo GetMessage("C2IT_FROM")?></td>
				<td align="center"><?echo GetMessage("C2IT_TO")?></td>
				<td align="center"><?echo GetMessage("C2IT_PRICE")?></td>
				<td align="center"><?echo GetMessage("C2IT_CURRENCY")?></td>
			</tr>
			</thead>
			<tbody id="container3">
				<?
				$ind = -1;
				$dbBasePrice = CPrice::GetList(
					array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
					array("PRODUCT_ID" => $PRODUCT_ID, "BASE" => "Y")
				);
				$arBasePrice = $dbBasePrice->Fetch();

				for ($i = 0, $intCount = count($arPriceBoundaries); $i < $intCount; $i++)
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
						$str_CAT_BASE_QUANTITY_FROM = ${"CAT_BASE_QUANTITY_FROM_".$ind};
						$str_CAT_BASE_QUANTITY_TO = ${"CAT_BASE_QUANTITY_TO_".$ind};
						$str_CAT_BASE_PRICE = ${"CAT_BASE_PRICE_".$ind};
						$str_CAT_BASE_CURRENCY = ${"CAT_BASE_CURRENCY_".$ind};
					}
					if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
						$boolExistPrice = true;
					$arCatPricesExist[$ind][$arBaseGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
					?>
				<tr id="model3">
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_FROM) : "") ?>" size="3" OnChange="ChangeBaseQuantityEx(this)">
						<input type="hidden" name="CAT_BASE_ID[<?= $ind ?>]" value="<?= htmlspecialcharsbx($str_CAT_BASE_ID) ?>">
					</td>
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_TO_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_TO) : "") ?>" size="3" OnChange="ChangeBaseQuantityEx(this)">
					</td>
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_PRICE_<?= $ind ?>" name="CAT_BASE_PRICE_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="15" OnBlur="ChangeBasePriceEx(this)">
					</td>
					<td valign="top" align="center">
						<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
						<select id="CAT_BASE_CURRENCY_<?= $ind ?>" name="CAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeBaseCurrencyEx(this)">
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

				if ($bVarsFromForm && $ind < intval($CAT_ROW_COUNTER))
				{
					for ($i = $ind + 1; $i <= intval($CAT_ROW_COUNTER); $i++)
					{
						$boolExistPrice = false;
						$ind++;
						$str_CAT_BASE_QUANTITY_FROM = ${"CAT_BASE_QUANTITY_FROM_".$ind};
						$str_CAT_BASE_QUANTITY_TO = ${"CAT_BASE_QUANTITY_TO_".$ind};
						$str_CAT_BASE_PRICE = ${"CAT_BASE_PRICE_".$ind};
						$str_CAT_BASE_CURRENCY = ${"CAT_BASE_CURRENCY_".$ind};
						if (trim($str_CAT_BASE_PRICE) != '' && doubleval($str_CAT_BASE_PRICE) >= 0)
							$boolExistPrice = true;
						$arCatPricesExist[$ind][$arBaseGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
						?>
					<tr id="model3">
						<td valign="top" align="center">
							<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_FROM) : "") ?>" size="3" OnChange="ChangeBaseQuantityEx(this)">
							<input type="hidden" name="CAT_BASE_ID[<?= $ind ?>]" value="<?= 0 ?>">
						</td>
						<td valign="top" align="center">
							<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_TO_<?= $ind ?>" value="<?echo ($str_CAT_BASE_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_BASE_QUANTITY_TO) : "") ?>" size="3" OnChange="ChangeBaseQuantityEx(this)">
						</td>
						<td valign="top" align="center">
							<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_PRICE_<?= $ind ?>" name="CAT_BASE_PRICE_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_BASE_PRICE) ?>" size="15" OnBlur="ChangeBasePriceEx(this)">
						</td>
						<td valign="top" align="center">
							<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
							<select id="CAT_BASE_CURRENCY_<?= $ind ?>" name="CAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeBaseCurrencyEx(this)">
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
				<tr id="model3">
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_FROM_<?= $ind ?>" value="" size="3" OnChange="ChangeBaseQuantityEx(this)">
					</td>
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_BASE_QUANTITY_TO_<?= $ind ?>" value="" size="3" OnChange="ChangeBaseQuantityEx(this)">
					</td>
					<td valign="top" align="center">
						<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_PRICE_<?= $ind ?>" name="CAT_BASE_PRICE_<?= $ind ?>" value="" size="15" OnBlur="ChangeBasePriceEx(this)">
					</td>
					<td valign="top" align="center">
						<?$db_curr = CCurrency::GetList(($by1="sort"), ($order1="asc"));?>
						<select id="CAT_BASE_CURRENCY_<?= $ind ?>" name="CAT_BASE_CURRENCY_<?= $ind ?>" <?if ($bReadOnly) echo "disabled readonly" ?> OnChange="ChangeBaseCurrencyEx(this)">
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
		<input type="hidden" name="CAT_ROW_COUNTER" id="CAT_ROW_COUNTER" value="<?= $ind ?>">
		<input type="button" value="<?echo GetMessage("C2IT_MORE")?>" OnClick="ClonePriceSections()">
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
			<?echo GetMessage("C2IT_PRICE_TYPE")?> "<? echo htmlspecialcharsbx(!empty($arCatalogGroup['NAME_LANG']) ? $arCatalogGroup['NAME_LANG'] : $arCatalogGroup["NAME"]); ?>":
		</td>
		<td valign="top" align="left">
			<table border="0" cellspacing="1" cellpadding="3" id="OTHER_PRICE_GROUP_TABLE_<?= $arCatalogGroup["ID"] ?>">
				<thead>
				<tr>
					<td align="center"><?echo GetMessage("C2IT_FROM")?></td>
					<td align="center"><?echo GetMessage("C2IT_TO")?></td>
					<td align="center"><?echo GetMessage("C2IT_NAC_TYPE")?></td>
					<td align="center"><?echo GetMessage("C2IT_PRICE")?></td>
					<td align="center"><?echo GetMessage("C2IT_CURRENCY")?></td>
				</tr>
				</thead>
				<tbody id="container3_<?= $arCatalogGroup["ID"] ?>">
					<?
					$ind = -1;
					$dbPriceList = CPrice::GetList(
						array("QUANTITY_FROM" => "ASC", "QUANTITY_TO" => "ASC"),
						array("PRODUCT_ID" => $PRODUCT_ID, "CATALOG_GROUP_ID" => $arCatalogGroup["ID"])
					);
					$arPrice = $dbPriceList->Fetch();
					for ($i = 0, $intCount = count($arPriceBoundaries); $i < $intCount; $i++)
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
							$str_CAT_EXTRA = ${"CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind};
							$str_CAT_PRICE = ${"CAT_PRICE_".$arCatalogGroup["ID"]."_".$ind};
							$str_CAT_CURRENCY = ${"CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind};
							$str_CAT_QUANTITY_FROM = ${"CAT_BASE_QUANTITY_FROM_".$ind};
							$str_CAT_QUANTITY_TO = ${"CAT_BASE_QUANTITY_TO_".$ind};
						}
						if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
							$boolExistPrice = true;
						$arCatPricesExist[$ind][$arCatalogGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
						?>
					<tr id="model3_<?= $arCatalogGroup["ID"] ?>">
						<td valign="top" align="center">
							<input type="text" disabled readonly id="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_FROM) : "") ?>" size="3">
							<input type="hidden" name="CAT_ID_<?= $arCatalogGroup["ID"] ?>[<?= $ind ?>]" value="<?= htmlspecialcharsbx($str_CAT_ID) ?>">
						</td>
						<td valign="top" align="center">
							<input type="text" disabled readonly id="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_TO) : "") ?>" size="3">

						</td>
						<td valign="top" align="center">
							<?
							echo CExtra::SelectBox("CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
							?>

						</td>
						<td valign="top" align="center">
							<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="10" OnChange="ptPriceChangeEx(this)">

						</td>
						<td valign="top" align="center">

							<?= CCurrency::SelectBox("CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>
							<script type="text/javascript">
								jsUtils.addEvent(window, 'load', function() {ChangeExtraEx(document.getElementById('CAT_EXTRA_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>'));});
							</script>

						</td>
					</tr>
						<?
					}

					if ($bVarsFromForm && $ind < intval(${"CAT_ROW_COUNTER_".$arCatalogGroup["ID"]}))
					{
						for ($i = $ind + 1; $i <= intval(${"CAT_ROW_COUNTER_".$arCatalogGroup["ID"]}); $i++)
						{
							$boolExistPrice = false;
							$ind++;
							$str_CAT_QUANTITY_FROM = ${"CAT_BASE_QUANTITY_FROM_".$ind};
							$str_CAT_QUANTITY_TO = ${"CAT_BASE_QUANTITY_TO_".$ind};
							$str_CAT_EXTRA = ${"CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind};
							$str_CAT_PRICE = ${"CAT_PRICE_".$arCatalogGroup["ID"]."_".$ind};
							$str_CAT_CURRENCY = ${"CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind};
							if (trim($str_CAT_PRICE) != '' && doubleval($str_CAT_PRICE) >= 0)
								$boolExistPrice = true;
							$arCatPricesExist[$ind][$arCatalogGroup['ID']] = ($boolExistPrice == true ? 'Y' : 'N');
							?>
						<tr id="model3_<?= $arCatalogGroup["ID"] ?>">
							<td valign="top" align="center">
								<input type="text" disabled readonly id="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_FROM != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_FROM) : "") ?>" size="3">
								<input type="hidden" name="CAT_ID_<?= $arCatalogGroup["ID"] ?>[<?= $ind ?>]" value="<?= 0 ?>">
							</td>
							<td valign="top" align="center">
								<input type="text" disabled readonly id="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo ($str_CAT_QUANTITY_TO != 0 ? htmlspecialcharsbx($str_CAT_QUANTITY_TO) : "") ?>" size="3">

							</td>
							<td valign="top" align="center">
								<?
								echo CExtra::SelectBox("CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_EXTRA, GetMessage("VAL_NOT_SET"), "ChangeExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
								?>

							</td>
							<td valign="top" align="center">
								<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="<?echo htmlspecialcharsbx($str_CAT_PRICE) ?>" size="10" OnChange="ptPriceChangeEx(this)">

							</td>
							<td valign="top" align="center">

								<?= CCurrency::SelectBox("CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, $str_CAT_CURRENCY, GetMessage("VAL_BASE"), false, "ChangeCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>
								<script type="text/javascript">
									jsUtils.addEvent(window, 'load', function () {ChangeExtraEx(document.getElementById('CAT_EXTRA_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>'));});
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
					<tr id="model3_<?= $arCatalogGroup["ID"] ?>">
						<td valign="top" align="center">
							<input type="text" disabled readonly id="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_FROM_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="3">
						</td>
						<td valign="top" align="center">
							<input type="text" disabled readonly id="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_QUANTITY_TO_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="3">

						</td>
						<td valign="top" align="center">
							<?
							echo CExtra::SelectBox("CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind, "", GetMessage("VAL_NOT_SET"), "ChangeExtraEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_EXTRA_".$arCatalogGroup["ID"]."_".$ind.'" ');
							?>

						</td>
						<td valign="top" align="center">
							<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" name="CAT_PRICE_<?= $arCatalogGroup["ID"] ?>_<?= $ind ?>" value="" size="10" OnChange="ptPriceChangeEx(this)">

						</td>
						<td valign="top" align="center">

							<?= CCurrency::SelectBox("CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind, "", GetMessage("VAL_BASE"), false, "ChangeCurrencyEx(this)", (($bReadOnly) ? "disabled readonly" : "").' id="'."CAT_CURRENCY_".$arCatalogGroup["ID"]."_".$ind.'" ') ?>

						</td>
					</tr>
						<?
						$arCatPricesExist[$ind][$arCatalogGroup['ID']] = 'N';
					}
					?>
				</tbody>
			</table>
			<input type="hidden" name="CAT_ROW_COUNTER_<?= $arCatalogGroup["ID"] ?>" id="CAT_ROW_COUNTER_<?= $arCatalogGroup["ID"] ?>" value="<?= $ind ?>">
		</td>
	</tr>
		<?
	}
	?>
</table>
<div id="ext_price_exist">
	<?
	foreach ($arCatPricesExist as $ind => $arPriceExist)
	{
		$strExist = (in_array('Y',$arPriceExist) ? 'Y' : 'N');
		?><input type="hidden" name="CAT_PRICE_EXIST_<? echo $ind; ?>" id="CAT_PRICE_EXIST_<? echo $ind; ?>" value="<? echo $strExist; ?>"><?
	}
	?>
</div>
</div>
	<?
	$tabControl1->BeginNextTab();
	?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" class="edit-table" id="catalog_properties_table">
			<tr id="CAT_BASE_QUANTITY2">
				<td width="40%">
					<?echo GetMessage("BASE_QUANTITY")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_QUANTITY = $arBaseProduct["QUANTITY"];
					if ($bVarsFromForm) $str_CAT_BASE_QUANTITY = $CAT_BASE_QUANTITY;
					?>
					<input type="text" id="CAT_BASE_QUANTITY" name="CAT_BASE_QUANTITY" <?if ($bReadOnly) echo "disabled readonly" ?> value="<?echo htmlspecialcharsbx($str_CAT_BASE_QUANTITY) ?>" size="30">

				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("BASE_TRACE")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_QUANTITY_TRACE = $arBaseProduct["QUANTITY_TRACE_ORIG"];
					if ($bVarsFromForm) $str_CAT_BASE_QUANTITY_TRACE = $CAT_BASE_QUANTITY_TRACE;
					$availQuantityTrace = COption::GetOptionString("catalog", "default_quantity_trace", 'N');
					?>
					<select id="CAT_BASE_QUANTITY_TRACE" name="CAT_BASE_QUANTITY_TRACE" <?if ($bReadOnly) echo "disabled readonly" ?>>
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
					if ($bVarsFromForm) $str_CAT_BASE_CAN_BUY_ZERO = $USE_STORE;
					$availCanBuyZero = COption::GetOptionString("catalog", "default_can_buy_zero", 'N');
					?>
					<select id="USE_STORE" name="USE_STORE" <?if ($bReadOnly) echo "disabled readonly" ?>>
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
					if ($bVarsFromForm) $str_CAT_BASE_NEGATIVE_AMOUNT_TRACE = $NEGATIVE_AMOUNT;
					$availNegativeAmountGlobal = COption::GetOptionString("catalog", "allow_negative_amount", 'N');
					?>
					<select id="NEGATIVE_AMOUNT" name="NEGATIVE_AMOUNT" <?if ($bReadOnly) echo "disabled readonly" ?>>
						<option value="D" <?if ("D"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_DEFAULT_NEGATIVE")." ("?><?echo $availNegativeAmountGlobal=='Y' ? GetMessage("C2IT_YES_NEGATIVE") : GetMessage("C2IT_NO_NEGATIVE")?>) </option>
						<option value="Y" <?if ("Y"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_YES_NEGATIVE")?></option>
						<option value="N" <?if ("N"==$str_CAT_BASE_NEGATIVE_AMOUNT_TRACE) echo " selected"?>><?=GetMessage("C2IT_NO_NEGATIVE")?></option>
					</select>
				</td>
			</tr>
			<tr>
				<td width="40%">
					<?echo GetMessage("BASE_WEIGHT")?>:
				</td>
				<td width="60%">
					<?
					$str_CAT_BASE_WEIGHT = $arBaseProduct["WEIGHT"];
					if ($bVarsFromForm) $str_CAT_BASE_WEIGHT = $CAT_BASE_WEIGHT;
					?>
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_BASE_WEIGHT" name="CAT_BASE_WEIGHT" value="<?echo htmlspecialcharsbx($str_CAT_BASE_WEIGHT) ?>" size="30">
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
						function ChangePriceType()
						{
							if (bReadOnly)
								return;

							var e_pt = document.getElementById('CAT_PRICE_TYPE');

							if (e_pt.options[e_pt.selectedIndex].value == "S")
							{
								document.getElementById('CAT_RECUR_SCHEME_TYPE').disabled = true;
								document.getElementById('CAT_RECUR_SCHEME_LENGTH').disabled = true;
								document.getElementById('CAT_TRIAL_PRICE_ID').disabled = true;
								document.getElementById('CAT_TRIAL_PRICE_ID_BUTTON').disabled = true;
							}
							else
							{
								if (e_pt.options[e_pt.selectedIndex].value == "R")
								{
									document.getElementById('CAT_RECUR_SCHEME_TYPE').disabled = false;
									document.getElementById('CAT_RECUR_SCHEME_LENGTH').disabled = false;
									document.getElementById('CAT_TRIAL_PRICE_ID').disabled = true;
									document.getElementById('CAT_TRIAL_PRICE_ID_BUTTON').disabled = true;
								}
								else
								{
									document.getElementById('CAT_RECUR_SCHEME_TYPE').disabled = false;
									document.getElementById('CAT_RECUR_SCHEME_LENGTH').disabled = false;
									document.getElementById('CAT_TRIAL_PRICE_ID').disabled = false;
									document.getElementById('CAT_TRIAL_PRICE_ID_BUTTON').disabled = false;
								}
							}
						}
					</script>

					<?
					$str_CAT_PRICE_TYPE = $arBaseProduct["PRICE_TYPE"];
					if ($bVarsFromForm) $str_CAT_PRICE_TYPE = $CAT_PRICE_TYPE;
					?>
					<select id="CAT_PRICE_TYPE" name="CAT_PRICE_TYPE" OnChange="ChangePriceType()">
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
					if ($bVarsFromForm) $str_CAT_RECUR_SCHEME_LENGTH = $CAT_RECUR_SCHEME_LENGTH;
					?>
					<input type="text" <?if ($bReadOnly) echo "disabled readonly" ?> id="CAT_RECUR_SCHEME_LENGTH" name="CAT_RECUR_SCHEME_LENGTH" value="<?echo htmlspecialcharsbx($str_CAT_RECUR_SCHEME_LENGTH) ?>" size="10">

				</td>
			</tr>
			<tr>
				<td>
					<?echo GetMessage("C2IT_PERIOD_TIME")?>
				</td>
				<td>
					<?
					$str_CAT_RECUR_SCHEME_TYPE = $arBaseProduct["RECUR_SCHEME_TYPE"];
					if ($bVarsFromForm) $str_CAT_RECUR_SCHEME_TYPE = $CAT_RECUR_SCHEME_TYPE;
					?>
					<select id="CAT_RECUR_SCHEME_TYPE" name="CAT_RECUR_SCHEME_TYPE">
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
					if ($bVarsFromForm) $str_CAT_TRIAL_PRICE_ID = $CAT_TRIAL_PRICE_ID;
					$catProductName = "";
					if (intval($str_CAT_TRIAL_PRICE_ID) > 0)
					{
						$dbCatElement = CIBlockElement::GetByID(intval($str_CAT_TRIAL_PRICE_ID));
						if ($arCatElement = $dbCatElement->GetNext())
							$catProductName = $arCatElement["NAME"];
					}
					?>
					<input id="CAT_TRIAL_PRICE_ID" name="CAT_TRIAL_PRICE_ID" value="<?= htmlspecialcharsbx($str_CAT_TRIAL_PRICE_ID) ?>" size="5" type="text"><input type="button" id="CAT_TRIAL_PRICE_ID_BUTTON" name="CAT_TRIAL_PRICE_ID_BUTTON" value="..." onClick="window.open('cat_product_search.php?IBLOCK_ID=<?= $IBLOCK_ID ?>&amp;field_name=CAT_TRIAL_PRICE_ID&amp;alt_name=trial_price_alt&amp;form_name='+getElementFormName(), '', 'scrollbars=yes,resizable=yes,width=600,height=500,top='+Math.floor((screen.height - 500)/2-14)+',left='+Math.floor((screen.width - 600)/2-5));">&nbsp;<span id="trial_price_alt"><?= $catProductName ?></span>
				</td>
			</tr>
			<tr>
				<td>
					<?echo GetMessage("C2IT_WITHOUT_ORDER")?>
				</td>
				<td>
					<?
					$str_CAT_WITHOUT_ORDER = $arBaseProduct["WITHOUT_ORDER"];
					if ($bVarsFromForm) $str_CAT_WITHOUT_ORDER = $CAT_WITHOUT_ORDER;
					?>
					<input type="checkbox" <?if ($bReadOnly) echo "disabled readonly" ?> name="CAT_WITHOUT_ORDER" value="Y" <?if ($str_CAT_WITHOUT_ORDER=="Y") echo "checked"?>>
				</td>
			</tr>
			<?
			}
			?>
		</table>
<script type="text/javascript">
	SetFieldsStyle('catalog_properties_table');
</script>
		<?if ($arCatalog["SUBSCRIPTION"]=="Y"):?>
<script type="text/javascript">
	ChangePriceType();
</script>
<?endif;?>

		<?if ($arCatalog["SUBSCRIPTION"]=="Y"):?>

<?
	$tabControl1->BeginNextTab();
	?>
<script type="text/javascript">
	function CatGroupsActivate(obj, id)
	{
		if (bReadOnly)
			return;

		var ed = document.getElementById('CAT_ACCESS_LENGTH_' + id);
		var ed1 = document.getElementById('CAT_ACCESS_LENGTH_TYPE_' + id);
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
			if (isset(${"CAT_USER_GROUP_ID_".$arGroup["ID"]}) && ${"CAT_USER_GROUP_ID_".$arGroup["ID"]} == "Y")
			{
				$arCurProductGroups[$arGroup["ID"]] = array(intval(${"CAT_ACCESS_LENGTH_".$arGroup["ID"]}), ${"CAT_ACCESS_LENGTH_TYPE_".$arGroup["ID"]});
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

				<input type="checkbox" name="CAT_USER_GROUP_ID_<?= $arGroup["ID"] ?>" value="Y"<?if (array_key_exists($arGroup["ID"], $arCurProductGroups)) echo " checked";?> onclick="CatGroupsActivate(this, <?= $arGroup["ID"] ?>)">

			</td>
			<td align="left"><? echo htmlspecialcharsbx($arGroup["NAME"]); ?></td>
			<td align="center">

				<input type="text" id="CAT_ACCESS_LENGTH_<?= $arGroup["ID"] ?>" name="CAT_ACCESS_LENGTH_<?= $arGroup["ID"] ?>" size="5" <?
					if (array_key_exists($arGroup["ID"], $arCurProductGroups))
						echo 'value="'.$arCurProductGroups[$arGroup["ID"]][0].'" ';
					else
						echo 'disabled ';
					?>>
				<select id="CAT_ACCESS_LENGTH_TYPE_<?= $arGroup["ID"] ?>" name="CAT_ACCESS_LENGTH_TYPE_<?= $arGroup["ID"] ?>"<?
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
<?endif;

	if(CBXFeatures::IsFeatureEnabled('CatMultiStore'))
	{
		$tabControl1->BeginNextTab();
		?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="internal">
			<tr class="heading">
				<td><?echo GetMessage("C2IT_STORE_NUMBER"); ?></td>
				<td><?echo GetMessage("C2IT_NAME"); ?></td>
				<td><?echo GetMessage("C2IT_STORE_ADDR"); ?></td>
				<td><?echo GetMessage("C2IT_PROD_AMOUNT"); ?></td>
			</tr>
		<?
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
				<td style="text-align:center;"><input type="text" id="AR_AMOUNT" name="AR_AMOUNT[<?=$arProp['ID']?>]" size="12" value="<?=$amount?>" /></td>
				<input type="hidden" name="AR_STORE_ID[<?=$arProp['ID']?>]" value="<?=$arProp['ID']?>" />
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

	$tabControl1->BeginNextTab();

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
	$tabControl1->End();
	?>
	</td>
</tr>
<?
}
?>