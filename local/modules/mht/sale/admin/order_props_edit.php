<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions < "W")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

ClearVars();
ClearVars("f_");
ClearVars("l_");

$ID = IntVal($ID);
$PERSON_TYPE_ID = IntVal($PERSON_TYPE_ID);

$arPersonTypeList = array();
$dbPersonType = CSalePersonType::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array());
while ($arPersonType = $dbPersonType->Fetch())
{
	$arPersonTypeList[$arPersonType["ID"]] = Array("ID" => $arPersonType["ID"], "NAME" => htmlspecialcharsEx($arPersonType["NAME"]), "LID" => implode(", ", $arPersonType["LIDS"]));
}

if ($ID > 0 && ($arOrderProps = CSaleOrderProps::GetByID($ID)))
{
	$PERSON_TYPE_ID = $arOrderProps["PERSON_TYPE_ID"];
}
elseif ($PERSON_TYPE_ID > 0 && !empty($arPersonTypeList[$PERSON_TYPE_ID]))
{
	$ID = 0;
}
else
{
	LocalRedirect("sale_order_props.php?lang=".LANG.GetFilterParams("filter_", false));
}

$strError = "";
$bInitVars = false;
if ((array_key_exists('save', $_POST) || array_key_exists('apply', $_POST) || array_key_exists('propeditmore', $_POST)) && $_SERVER['REQUEST_METHOD']=="POST"
	&& $saleModulePermissions=="W"   && check_bitrix_sessid())
{
	$PERSON_TYPE_ID = IntVal($PERSON_TYPE_ID);
	if ($PERSON_TYPE_ID<=0)
		$strError .= GetMessage("ERROR_NO_PERS_TYPE")."<br>";

	$NAME = Trim($NAME);
	if (strlen($NAME)<=0)
		$strError .= GetMessage("ERROR_NO_NAME")."<br>";

	$TYPE = Trim($TYPE);
	if (strlen($TYPE)<=0)
		$strError .= GetMessage("ERROR_NO_TYPE")."<br>";

	if ($REQUIED!="Y") $REQUIED = "N";
	if ($USER_PROPS!="Y") $USER_PROPS = "N";
	if ($MULTIPLE != "Y" || $TYPE != "FILE") $MULTIPLE = "N";

	if ($IS_LOCATION!="Y") $IS_LOCATION = "N";
	if ($IS_LOCATION4TAX!="Y") $IS_LOCATION4TAX = "N";
	if ($IS_EMAIL!="Y") $IS_EMAIL = "N";
	if ($IS_PROFILE_NAME!="Y") $IS_PROFILE_NAME = "N";
	if ($IS_PAYER!="Y") $IS_PAYER = "N";
	if ($IS_FILTERED!="Y") $IS_FILTERED = "N";
	if ($IS_ZIP!="Y") $IS_ZIP = "N";
	if ($ACTIVE!="Y") $ACTIVE = "N";
	if ($UTIL!="Y") $UTIL = "N";

	if ($IS_LOCATION=="Y" && $TYPE!="LOCATION")
		$strError .= GetMessage("ERROR_NOT_LOCATION")."<br>";
	if ($IS_LOCATION4TAX=="Y" && $TYPE!="LOCATION")
		$strError .= GetMessage("ERROR_NOT_LOCATION")."<br>";

	if ($IS_EMAIL=="Y" && $TYPE!="TEXT")
		$strError .= GetMessage("ERROR_NOT_EMAIL")."<br>";
	if ($IS_PROFILE_NAME=="Y" && $TYPE!="TEXT")
		$strError .= GetMessage("ERROR_NOT_PROFILE_NAME")."<br>";
	if ($IS_PAYER=="Y" && $TYPE!="TEXT")
		$strError .= GetMessage("ERROR_NOT_PAYER")."<br>";
	if ($IS_ZIP=="Y" && $TYPE!="TEXT")
		$strError .= GetMessage("ERROR_NOT_ZIP")."<br>";

	if ($IS_LOCATION == "Y" && $TYPE == "LOCATION")
		$INPUT_FIELD_LOCATION = IntVal($INPUT_FIELD_LOCATION);
	else
		$INPUT_FIELD_LOCATION = "";

	if ($TYPE == "MULTISELECT" || $TYPE == "FILE")
	{
		$IS_FILTERED = "N";
		$IS_LOCATION = "N";
		$IS_LOCATION4TAX = "N";
		$IS_EMAIL = "N";
		$IS_PROFILE_NAME = "N";
		$IS_PAYER = "N";
		$IS_FILTERED = "N";
		$IS_ZIP = "N";
	}

	$SORT = IntVal($SORT);
	if ($SORT<=0) $SORT = 100;

	$PROPS_GROUP_ID = IntVal($PROPS_GROUP_ID);
	if ($PROPS_GROUP_ID<=0)
		$strError .= GetMessage("ERROR_NO_GROUP")."<br>";

	if (strlen($strError)<=0)
	{
		unset($arFields);
		$arFields = array(
			"PERSON_TYPE_ID" => $PERSON_TYPE_ID,
			"NAME" => $NAME,
			"TYPE" => $TYPE,
			"REQUIED" => $REQUIED,
			"DEFAULT_VALUE" => $DEFAULT_VALUE,
			"SORT" => $SORT,
			"CODE" => (strlen($CODE)<=0 ? False : $CODE),
			"USER_PROPS" => $USER_PROPS,
			"IS_LOCATION" => $IS_LOCATION,
			"IS_LOCATION4TAX" => $IS_LOCATION4TAX,
			"PROPS_GROUP_ID" => $PROPS_GROUP_ID,
			"SIZE1" => $SIZE1,
			"SIZE2" => $SIZE2,
			"DESCRIPTION" => $DESCRIPTION,
			"IS_EMAIL" => $IS_EMAIL,
			"IS_PROFILE_NAME" => $IS_PROFILE_NAME,
			"IS_PAYER" => $IS_PAYER,
			"IS_FILTERED" => $IS_FILTERED,
			"IS_ZIP" => $IS_ZIP,
			"ACTIVE" => $ACTIVE,
			"UTIL" => $UTIL,
			"INPUT_FIELD_LOCATION" => $INPUT_FIELD_LOCATION,
			"MULTIPLE" => $MULTIPLE
		);

		if ($ID>0)
		{
			if (!CSaleOrderProps::Update($ID, $arFields))
				$strError .= GetMessage("ERROR_EDIT_PROP")."<br>";

			if (strlen($strError)<=0)
			{
				//$db_order_props_tmp = CSaleOrderPropsValue::GetList(($b="NAME"), ($o="ASC"), Array("ORDER_PROPS_ID"=>$ID));
				$db_order_props_tmp = CSaleOrderPropsValue::GetList(($b = "ID"), ($o = "ASC"), Array("ORDER_PROPS_ID" => $ID, "!CODE" => (strlen($CODE)<=0 ? False : $CODE)));
				while ($ar_order_props_tmp = $db_order_props_tmp->Fetch())
				{
					CSaleOrderPropsValue::Update($ar_order_props_tmp["ID"], array("CODE"=>(strlen($CODE)<=0 ? False : $CODE)));
				}
			}
		}
		else
		{
			$ID = CSaleOrderProps::Add($arFields);
			if ($ID<=0)
				$strError .= GetMessage("ERROR_ADD_PROP")."<br>";
		}
	}

	if (strlen($strError) <= 0)
	{
		if (isset($_POST["PAY_SYSTEM_ID"]) && is_array($_POST["PAY_SYSTEM_ID"]) && isset($_POST["DELIVERY_SYSTEM_ID"]) && is_array($_POST["DELIVERY_SYSTEM_ID"]))
		{
			$_POST["PAY_SYSTEM_ID"] = array_filter($_POST["PAY_SYSTEM_ID"]);
			$_POST["DELIVERY_SYSTEM_ID"] = array_filter($_POST["DELIVERY_SYSTEM_ID"]);

			if ((count($_POST["PAY_SYSTEM_ID"]) > 0) || count($_POST["DELIVERY_SYSTEM_ID"]) > 0)
			{
				if ($IS_LOCATION4TAX == "Y") // in some cases relations are not allowed
				{
					$strError .= GetMessage("ERROR_LOCATION4TAX_RELATION_NOT_ALLOWED")."<br>";
				}
				else if ($IS_EMAIL == "Y")
				{
					$strError .= GetMessage("ERROR_EMAIL_RELATION_NOT_ALLOWED")."<br>";
				}
				else if ($IS_PROFILE_NAME == "Y")
				{
					$strError .= GetMessage("ERROR_PROFILE_NAME_RELATION_NOT_ALLOWED")."<br>";
				}
			}

			if (strlen($strError) <= 0)
			{
				CSaleOrderProps::UpdateOrderPropsRelations($ID, $_POST["PAY_SYSTEM_ID"], "P");
				CSaleOrderProps::UpdateOrderPropsRelations($ID, $_POST["DELIVERY_SYSTEM_ID"], "D");
			}
		}
	}

	if (strlen($strError)<=0)
	{
		if ($TYPE=="SELECT" || $TYPE=="MULTISELECT" || $TYPE=="RADIO")
		{
			$numpropsvals = IntVal($numpropsvals);
			for ($i = 0; $i<=$numpropsvals; $i++)
			{
				$strError1 = "";

				$CF_ID = IntVal(${"ID_".$i});
				$CF_DEL = ${"DELETE_".$i};
				unset($arFieldsV);
				$arFieldsV = array(
					"ORDER_PROPS_ID" => $ID,
					"VALUE" => Trim(${"VALUE_".$i}),
					"NAME" => Trim(${"NAME_".$i}),
					"SORT" => ( (IntVal(${"SORT_".$i})>0) ? IntVal(${"SORT_".$i}) : 100 ),
					"DESCRIPTION" => Trim(${"DESCRIPTION_".$i})
					);

				if ($CF_ID<=0)
				{
					if(!isset($arFieldsV["VALUE"]) || strval($arFieldsV["VALUE"]) == '')
					{
						$arFieldsV["VALUE"] = md5(uniqid(""));
					}

					if (strlen($arFieldsV["VALUE"])>0 && strlen($arFieldsV["NAME"])>0)
					{
						if (!CSaleOrderPropsVariant::Add($arFieldsV))
						{
							$strError1 .= GetMessage("ERROR_ADD_VARIANT")." (".$arFieldsV["VALUE"].", ".$arFieldsV["NAME"].", ".$arFieldsV["SORT"].", ".$arFieldsV["DESCRIPTION"].").<br>";
						}
					}
				}
				elseif ($CF_DEL=="Y")
				{
					CSaleOrderPropsVariant::Delete($CF_ID);
				}
				else
				{
					if (strlen($arFieldsV["VALUE"])<=0)
						$strError1 .= GetMessage("ERROR_EMPTY_VAR_CODE")." (".$arFieldsV["NAME"].").<br>";

					if (strlen($arFieldsV["NAME"])<=0)
						$strError1 .= GetMessage("ERROR_EMPTY_VAR_NAME")." (".$arFieldsV["VALUE"].").<br>";

					if (strlen($strError1)<=0)
					{
						if (!CSaleOrderPropsVariant::Update($CF_ID, $arFieldsV))
							$strError .= GetMessage("ERROR_EDIT_VARIANT")." (".$arFieldsV["VALUE"].", ".$arFieldsV["NAME"].", ".$arFieldsV["SORT"].", ".$arFieldsV["DESCRIPTION"].").<br>";
					}
				}
				$strError .= $strError1;
			}
		}
		else
		{
			CSaleOrderPropsVariant::DeleteAll($ID);
		}
	}

	if (strlen($strError)>0) $bInitVars = True;

	if (strlen($save)>0 && strlen($strError)<=0)
		LocalRedirect("sale_order_props.php?lang=".LANG.GetFilterParams("filter_", false));
}

if ($ID>0)
{
	$db_orderProps = CSaleOrderProps::GetList(array("ID" => "ASC"), array("ID" => $ID));
	$db_orderProps->ExtractFields("str_");
}
else
{
	$str_ACTIVE = "Y";
	$str_IS_FILTERED = "Y";
}

if (strlen($propeditmore)>0) $bInitVars = True;

if ($bInitVars)
{
	$DB->InitTableVarsForEdit("b_sale_order_props", "", "str_");
}

if($ID > 0)
	$sDocTitle = GetMessage("SALE_EDIT_RECORD", array("#ID#"=>$ID));
else
	$sDocTitle = GetMessage("SALE_NEW_RECORD");
$APPLICATION->SetTitle($sDocTitle);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

/*********************************************************************/
/********************  BODY  *****************************************/
/*********************************************************************/
?>

<?
$aMenu = array(
	array(
		"TEXT" => GetMessage("SOPEN_2FLIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/sale_order_props.php?lang=".LANG.GetFilterParams("filter_")
	)
);

if ($ID > 0 && $saleModulePermissions >= "W")
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$arDDMenu = array();

	$arDDMenu[] = array(
		"TEXT" => "<b>".GetMessage("SOPEN_4NEW_PROMT")."</b>",
		"ACTION" => false
	);

	foreach($arPersonTypeList as $arRes)
	{
		$arDDMenu[] = array(
			"TEXT" => "[".$arRes["ID"]."] ".$arRes["NAME"]." (".htmlspecialcharsbx($arRes["LID"]).")",
			"ACTION" => "window.location = 'sale_order_props_edit.php?lang=".LANG."&PERSON_TYPE_ID=".$arRes["ID"]."';"
		);
	}

	$aMenu[] = array(
		"TEXT" => GetMessage("SOPEN_NEW_PROPS"),
		"ICON" => "btn_new",
		"MENU" => $arDDMenu
	);

	$aMenu[] = array(
		"TEXT" => GetMessage("SOPEN_DELETE_PROPS"),
		"LINK" => "javascript:if(confirm('".GetMessage("SOPEN_DELETE_PROPS_CONFIRM")."')) window.location='/bitrix/admin/sale_order_props.php?action=delete&ID[]=".$ID."&lang=".LANG."&".bitrix_sessid_get()."#tb';",
		"ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($strError);?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="form1">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<input type="hidden" name="PERSON_TYPE_ID" value="<?echo $PERSON_TYPE_ID ?>">
<?=bitrix_sessid_post()?>

<?
$arPersonType = $arPersonTypeList[$PERSON_TYPE_ID];

$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("SOPEN_TAB_PROPS"), "ICON" => "sale", "TITLE" => str_replace("#PTYPE#", $arPersonType["NAME"]." (".htmlspecialcharsEx($arPersonType["LID"]).")", GetMessage("SOPEN_TAB_PROPS_DESCR"))),
	array("DIV" => "edit2", "TAB" => GetMessage("SALE_PROPERTY_LINKING"), "ICON" => "sale", "TITLE" => GetMessage("SALE_PROPERTY_LINKING_DESC"))
);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
$disMulti = "";
if($str_TYPE == "MULTISELECT")
	$disMulti = " disabled";

?>

	<?if ($ID>0):?>
	<tr>
		<td width="40%">ID:</td>
		<td width="60%"><?echo $ID ?></td>
	</tr>
	<?endif;?>

	<tr>
		<td width="40%"><?echo GetMessage("SALE_PERS_TYPE")?>:</td>
		<td width="60%">
			[<?echo $arPersonType["ID"] ?>] <?echo $arPersonType["NAME"] ?> (<?echo htmlspecialcharsEx($arPersonType["LID"]) ?>)
		</td>
	</tr>

	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("F_NAME") ?>:</td>
		<td width="60%">
			<input type="text" name="NAME" value="<?echo $str_NAME ?>">
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_CODE") ?>:</td>
		<td width="60%">
			<input type="text" name="CODE" value="<?echo $str_CODE ?>">
		</td>
	</tr>
	<tr class="adm-detail-required-field">
		<td width="40%"><?echo GetMessage("F_TYPE") ?>:</td>
		<td width="60%">
			<select name="TYPE" onchange="changeOptions(this.value);">
				<?
				foreach ($SALE_FIELD_TYPES as $key => $value):
					?><option value="<?echo $key?>"<?if ($str_TYPE==$key) echo " selected"?>>[<?echo htmlspecialcharsbx($key) ?>] <?echo htmlspecialcharsbx($value) ?></option><?
				endforeach;
				?>
			</select>
			<script type="text/javascript">
				function changeOptions(val)
				{
					if (val == 'FILE')
					{
						BX("multiple").style.display = 'table-row';
					}
					else
					{
						BX("multiple").style.display = 'none';
					}
				}
			</script>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_REQUIED");?>:</td>
		<td width="60%">
			<input type="checkbox" name="REQUIED" value="Y" <?if ($str_REQUIED=="Y") echo "checked"?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_ACTIVE");?>:</td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y" <?if ($str_ACTIVE=="Y") echo "checked"?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_UTIL");?>:</td>
		<td width="60%">
			<input type="checkbox" name="UTIL" value="Y" <?if ($str_UTIL=="Y") echo "checked"?>>
		</td>
	</tr>
	<?
		$class = ($str_TYPE == "FILE") ? "adm-table-row-visible" : "adm-not-visible";
	?>
	<tr id="multiple" class="<?=$class?>">
		<td width="40%"><?echo GetMessage("F_MULTIPLE");?>:</td>
		<td width="60%">
			<input type="checkbox" name="MULTIPLE" value="Y" <?if ($str_MULTIPLE=="Y") echo "checked"?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_DEFAULT_VALUE");?>:</td>
		<td width="60%">
			<input type="text" name="DEFAULT_VALUE" value="<?echo $str_DEFAULT_VALUE ?>">
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_SORT");?>:</td>
		<td width="60%">
			<input type="text" name="SORT" value="<?echo $str_SORT ?>">
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_USER_PROPS");?>:</td>
		<td width="60%">
			<input type="checkbox" name="USER_PROPS" value="Y" <?if ($str_USER_PROPS=="Y") echo "checked"?>>
		</td>
	</tr>
	<tr>
		<td width="40%"><?echo GetMessage("F_PROPS_GROUP_ID") ?>:</td>
		<td width="60%">
			<select name="PROPS_GROUP_ID">
				<?
				$l = CSaleOrderPropsGroup::GetList(($b="NAME"), ($o="ASC"), Array("PERSON_TYPE_ID"=>$PERSON_TYPE_ID));
				while ($l->ExtractFields("l_")):
					?><option value="<?echo $l_ID?>"<?if (IntVal($str_PROPS_GROUP_ID)==IntVal($l_ID)) echo " selected"?>>[<?echo $l_ID ?>] <?echo $l_NAME?></option><?
				endwhile;
				?>
			</select>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="sale_order_props_group.php?lang=<?echo LANG?>" target="_blank"><b><?echo GetMessage("SALE_PROPS_GROUP")?> &gt;&gt;</b></a>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?echo GetMessage("F_SIZE1");?>:</td>
		<td width="60%" valign="top">
			<input type="text" name="SIZE1" value="<?echo $str_SIZE1 ?>"><br>
			<small><?echo GetMessage("F_SIZE1_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top"><?echo GetMessage("F_SIZE2");?>:</td>
		<td width="60%" valign="top">
			<input type="text" name="SIZE2" value="<?echo $str_SIZE2 ?>"><br>
			<small><?echo GetMessage("F_SIZE2_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_DESCRIPTION");?>:
		</td>
		<td width="60%" valign="top">
			<textarea rows="3" cols="40" name="DESCRIPTION"><?echo $str_DESCRIPTION;?></textarea>
		</td>
	</tr>

	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_LOCATION");?>:
		</td>
		<td width="60%" valign="top">
			<input onClick="fDisplayTextLocation();" type="checkbox" name="IS_LOCATION" value="Y" <?if ($str_IS_LOCATION=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_LOCATION_DESCR");?></small><br>
		</td>
	</tr>

	<?
	$arFilter = array("PERSON_TYPE_ID" => IntVal($arPersonType["ID"]), "TYPE" => "TEXT", "ACTIVE" => "Y");
	$dbAlterLocList = CSaleOrderProps::GetList(array(),	$arFilter, false, false, array("ID", "NAME"));
	?>
	<tr id="SHOW_TEXT_LOCATION">
		<td width="40%"><?echo GetMessage("F_ANOTHER_LOCATION") ?>:</td>
		<td width="60%">
			<select name="INPUT_FIELD_LOCATION" id="INPUT_FIELD_LOCATION">
				<option value=""><?echo GetMessage("NULL_ANOTHER_LOCATION") ?></option>
				<?
				while ($arAlterLocList = $dbAlterLocList->Fetch()):
					?><option value="<?echo $arAlterLocList["ID"]?>"<?if ($str_INPUT_FIELD_LOCATION==$arAlterLocList["ID"]) echo " selected"?>>[<?echo htmlspecialcharsbx($arAlterLocList["ID"]) ?>] <?echo htmlspecialcharsbx($arAlterLocList["NAME"]) ?></option><?
				endwhile;
				?>
			</select>
			<br><small><?echo GetMessage("F_INPUT_FIELD_DESCR");?></small><br>

			<script>
				function fDisplayTextLocation()
				{
					if (document.form1.IS_LOCATION.checked)
						document.getElementById('SHOW_TEXT_LOCATION').style.display = "table-row";
					else
						document.getElementById('SHOW_TEXT_LOCATION').style.display = "none";
				}
				fDisplayTextLocation();
			</script>
		</td>
	</tr>

	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_LOCATION4TAX");?>:
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_LOCATION4TAX" value="Y" <?if ($str_IS_LOCATION4TAX=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_LOCATION4TAX_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_ZIP");?>:
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_ZIP" value="Y" <?if ($str_IS_ZIP=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_ZIP_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_EMAIL");?>:
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_EMAIL" value="Y" <?if ($str_IS_EMAIL=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_EMAIL_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_PROFILE_NAME");?>:
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_PROFILE_NAME" value="Y" <?if ($str_IS_PROFILE_NAME=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_PROFILE_NAME_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_PAYER");?>:
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_PAYER" value="Y" <?if ($str_IS_PAYER=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_PAYER_DESCR");?></small><br>
		</td>
	</tr>
	<tr>
		<td width="40%" valign="top">
			<?echo GetMessage("F_IS_FILTERED");?>
		</td>
		<td width="60%" valign="top">
			<input type="checkbox" name="IS_FILTERED" value="Y" <?if ($str_IS_FILTERED=="Y") echo "checked"?><?=$disMulti?>><br>
			<small><?echo GetMessage("F_IS_FILTERED_DESCR");?></small><br>
		</td>
	</tr>

<?if ($str_TYPE=="SELECT" || $str_TYPE=="MULTISELECT" || $str_TYPE=="RADIO"):?>
	<tr class="heading">
		<td colspan="2">
			<?if (strlen($propeditmore)>0):?><a name="tb"></a><?endif;?>
			<?echo GetMessage("SALE_VARIANTS")?>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
			<table cellspacing="0" class="internal">
				<tr class="heading">
					<td align="center"><?echo GetMessage("SALE_VARIANTS_CODE")?></td>
					<td align="center"><?echo GetMessage("SALE_VARIANTS_NAME")?></td>
					<td align="center"><?echo GetMessage("SALE_VARIANTS_SORT")?></td>
					<td align="center"><?echo GetMessage("SALE_VARIANTS_DESCR")?></td>
					<td align="center"><?echo GetMessage("SALE_VARIANTS_DEL")?></td>
				</tr>
			<?
			$db_propsVars = CSaleOrderPropsVariant::GetList(($b="SORT"), ($o="ASC"), Array("ORDER_PROPS_ID"=>$ID));
			$ind = -1;
			$oldind = -1;
			while ($arPropsVars = $db_propsVars->GetNext())
			{

				$ind++;
				$oldind++;
				if ($bInitVars)
				{
					$DB->InitTableVarsForEdit("b_sale_order_props_variant", "", "f_", "_".$oldind);
				}
				?>
				<tr>
					<td>
						<input type="hidden" name="ID_<?echo $ind;?>" value="<?echo $arPropsVars['ID'];?>">
						<input type="text" name="VALUE_<?echo $ind;?>" value="<?echo htmlspecialcharsbx($arPropsVars['VALUE']);?>" size="5">
					</td>
					<td>
						<input type="text" name="NAME_<?echo $ind;?>" value="<?echo htmlspecialcharsbx($arPropsVars['NAME']);?>" size="30">
					</td>
					<td>
						<input type="text" name="SORT_<?echo $ind;?>" value="<?echo IntVal($arPropsVars['SORT']);?>" size="3">
					</td>
					<td>
						<input type="text" name="DESCRIPTION_<?echo $ind;?>" value="<?echo htmlspecialcharsbx($arPropsVars['DESCRIPTION']);?>" size="30">
					</td>
					<td>
						<input type="checkbox" name="DELETE_<?echo $ind;?>" value="Y">
					</td>
				</tr>
				<?
			}

			for ($i=0; $i<5; $i++)
			{
				$ind++;
				$oldind++;
				?>
				<tr>
					<td>
						<input type="hidden" name="ID_<?echo $ind;?>" value="new">
						<input type="text" name="VALUE_<?echo $ind;?>" value="" size="5">
					</td>
					<td>
						<input type="text" name="NAME_<?echo $ind;?>" value="" size="30">
					</td>
					<td>
						<input type="text" name="SORT_<?echo $ind;?>" value="" size="3">
					</td>
					<td>
						<input type="text" name="DESCRIPTION_<?echo $ind;?>" value="" size="30">
					</td>
					<td>
						&nbsp;
					</td>
				</tr>
				<?
			}
			?>
				<tr>
					<td colspan="4" align="right">
						<input type="hidden" name="numpropsvals" value="<?echo $ind; ?>">
						<input type="submit" name="propeditmore" value="<?echo GetMessage("SALE_VARIANTS_MORE")?>">
					</td>
					<td align="right">
						&nbsp;
					</td>
				</tr>
			</table>
		</td>
	</tr>
<?endif;?>

<?
// order property relations tab
$tabControl->BeginNextTab();
?>
	<!-- payment system relations control -->
	<tr>
		<td width="40%">
			<?=GetMessage("SALE_PROPERTY_PAYSYSTEM");?>:
		</td>
		<td width="60%">
			<select multiple="multiple" size="5" name="PAY_SYSTEM_ID[]">
			<?
			$arPaySystemID = array();

			if (isset($_POST["PAY_SYSTEM_ID"]) && is_array($_POST["PAY_SYSTEM_ID"]))
			{
				$arPaySystemID = $_POST["PAY_SYSTEM_ID"];
			}
			else
			{
				$dbRes = CSaleOrderProps::GetOrderPropsRelations(array("PROPERTY_ID" => $ID, "ENTITY_TYPE" => "P"));
				while ($arRes = $dbRes->Fetch())
					$arPaySystemID[] = $arRes["ENTITY_ID"];
			}
			?>
				<option value="" <?=(count($arPaySystemID) <= 0)?"selected":""?>><?=GetMessage("SALE_PROPERTY_SELECT_ALL");?></option>
			<?
			$dbResultList = CSalePaySystem::GetList(
				array("SORT"=>"ASC", "NAME"=>"ASC"),
				array("ACTIVE" => "Y"),
				false,
				false,
				array("ID", "NAME", "ACTIVE", "SORT", "LID")
			);
			while ($arPayType = $dbResultList->Fetch()):
				$psName = (strlen($arPayType["LID"]) > 0) ? $arPayType["NAME"]." (".$arPayType["LID"].")" : $arPayType["NAME"];
			?>
				<option value="<?=intval($arPayType["ID"]);?>" <?=(in_array($arPayType["ID"], $arPaySystemID)?"selected":"")?>><?=htmlspecialcharsbx($psName." [".$arPayType["ID"]."]")?></option>
			<?endwhile;?>
			</select>
		</td>
	</tr>

	<!-- delivery system relations control -->
	<tr>
		<td width="40%">
			<?=GetMessage("SALE_PROPERTY_DELIVERY");?>:
		</td>
		<td width="60%">
			<select multiple="multiple" size="5" name="DELIVERY_SYSTEM_ID[]">
				<?
				$arDeliverySystemID = array();

				if (isset($_POST["DELIVERY_SYSTEM_ID"]) && is_array($_POST["DELIVERY_SYSTEM_ID"]))
				{
					$arDeliverySystemID = $_POST["DELIVERY_SYSTEM_ID"];
				}
				else
				{
					$dbRes = CSaleOrderProps::GetOrderPropsRelations(array("PROPERTY_ID" => $ID, "ENTITY_TYPE" => "D"));
					while ($arRes = $dbRes->Fetch())
						$arDeliverySystemID[] = $arRes["ENTITY_ID"];
				}
				?>
					<option value="" <?=(count($arDeliverySystemID) <= 0)?"selected":""?>><?=GetMessage("SALE_PROPERTY_SELECT_ALL");?></option>
				<?
				$arDeliveryOptions = array();

				$dbResultList = CSaleDelivery::GetList(
					array("SORT"=>"ASC", "NAME"=>"ASC"),
					array("ACTIVE" => "Y"),
					false,
					false,
					array("ID", "NAME", "ACTIVE", "SORT")
				);
				while ($arDeliverySystem = $dbResultList->Fetch())
				{
					$selected = (in_array($arDeliverySystem["ID"], $arDeliverySystemID)) ? " selected=\"selected\"" : "";
					$arDeliveryOptions[] = "<option value=\"".intval($arDeliverySystem["ID"])."\"".$selected.">".htmlspecialcharsbx($arDeliverySystem["NAME"])." [".$arDeliverySystem["ID"]."]</option>";
				}

				$dbDeliveryServices = CSaleDeliveryHandler::GetList(
					array("SORT" => "ASC"),
					array("SITE_ID" => trim($arPersonType["LID"]))
				);
				while ($arDeliveryService = $dbDeliveryServices->GetNext())
				{
					$dsName = (strlen($arDeliveryService["LID"]) > 0) ? " (".$arDeliveryService["LID"].")" : "";

					foreach ($arDeliveryService["PROFILES"] as $profileId => $arDeliveryProfile)
					{
						if ($arDeliveryProfile["ACTIVE"] != "Y")
							continue;

						$id = $arDeliveryService["SID"].":".$profileId;
						$selected = (in_array($id, $arDeliverySystemID)) ? " selected=\"selected\"" : "";
						$arDeliveryOptions[] = "<option".$selected." value=\"".$id."\">".$arDeliveryService["NAME"]." (".$arDeliveryProfile["TITLE"].") [".$id."] ".$dsName."</option>";
					}
				}

				foreach ($arDeliveryOptions as $optionHTML)
					echo $optionHTML;
				?>
			</select>
		</td>
	</tr>

<?
// end of order property relations tab
$tabControl->EndTab();
?>

<?
$tabControl->Buttons(
		array(
				"disabled" => ($saleModulePermissions < "W"),
				"back_url" => "/bitrix/admin/sale_order_props.php?lang=".LANG.GetFilterParams("filter_")
			)
	);
?>

<?
$tabControl->End();
?>

</form>
<?require($DOCUMENT_ROOT."/bitrix/modules/main/include/epilog_admin.php");?>
