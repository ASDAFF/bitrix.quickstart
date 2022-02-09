<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!($USER->CanDoOperation('catalog_read') || $USER->CanDoOperation('catalog_vat')))
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$bReadOnly = !$USER->CanDoOperation('catalog_vat');

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/include.php");

if ($ex = $APPLICATION->GetException())
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	$strError = $ex->GetString();
	ShowError($strError);

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/catalog/prolog.php");

ClearVars();

$errorMessage = "";
$bVarsFromForm = false;

$ID = IntVal($ID);

if ($REQUEST_METHOD=="POST" && strlen($Update)>0 && !$bReadOnly && check_bitrix_sessid())
{
	$DB->StartTransaction();

	$arFields = Array(
		"ID" => $ID,
		"ACTIVE" => (($ACTIVE == "Y") ? "Y" : "N"),
		"C_SORT" => intval($C_SORT),
		"NAME" => $NAME,
		"RATE" => floatval($RATE),
	);

	if ($res = CCatalogVAT::Set($arFields))
	{
		$ID = $res;

		$DB->Commit();
		if (strlen($apply)<=0)
			LocalRedirect("/bitrix/admin/cat_vat_admin.php?lang=".LANG."&".GetFilterParams("filter_", false));
		else
			LocalRedirect("/bitrix/admin/cat_vat_edit.php?lang=".LANG."&ID=".$ID."&".GetFilterParams("filter_", false));
	}
	else
	{
		if ($ex = $APPLICATION->GetException())
			$errorMessage .= $ex->GetString()."<br>";
		else
			$errorMessage .= (0 < $ID ? str_replace('#ID#', $ID, GetMessage('CVAT_ERR_UPDATE')) : GetMessage('CVAT_ERR_ADD'))."<br>";
		$bVarsFromForm = true;
		$DB->Rollback();
	}
}

if ($ID > 0)
	$APPLICATION->SetTitle(str_replace("#ID#", $ID, GetMessage("CVAT_TITLE_UPDATE")));
else
	$APPLICATION->SetTitle(GetMessage("CVAT_TITLE_ADD"));

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$str_ACTIVE = "Y";

if ($ID > 0)
{
	$dbResult = CCatalogVAT::GetByID($ID);

	if (!$dbResult->ExtractFields("str_"))
		$ID = 0;
}

if ($bVarsFromForm)
	$DB->InitTableVarsForEdit("b_catalog_vat", "", "str_");

?>

<?
$aMenu = array(
	array(
		"TEXT" => GetMessage("CVAT_LIST"),
		"ICON" => "btn_list",
		"LINK" => "/bitrix/admin/cat_vat_admin.php?lang=".LANG."&".GetFilterParams("filter_", false)
	)
);

if ($ID > 0 && !$bReadOnly)
{
	$aMenu[] = array("SEPARATOR" => "Y");

	$aMenu[] = array(
		"TEXT" => GetMessage("CVAT_NEW"),
		"ICON" => "btn_new",
		"LINK" => "/bitrix/admin/cat_vat_edit.php?lang=".LANG."&".GetFilterParams("filter_", false)
	);

	$aMenu[] = array(
		"TEXT" => GetMessage("CVAT_DELETE"),
		"ICON" => "btn_delete",
		"LINK" => "javascript:if(confirm('".GetMessage("CVAT_DELETE_CONFIRM")."')) window.location='/bitrix/admin/cat_vat_admin.php?action=delete&ID[]=".$ID."&lang=".LANG."&".bitrix_sessid_get()."#tb';",
		"WARNING" => "Y"
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();
?>

<?CAdminMessage::ShowMessage($errorMessage);?>

<form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?" name="vat_edit">
<?echo GetFilterHiddens("filter_");?>
<input type="hidden" name="Update" value="Y">
<input type="hidden" name="lang" value="<?echo LANG ?>">
<input type="hidden" name="ID" value="<?echo $ID ?>">
<?=bitrix_sessid_post()?>

<?
$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("CVAT_TAB"), "ICON" => "catalog", "TITLE" => GetMessage("CVAT_TAB_DESCR")),
	);

$tabControl = new CAdminTabControl("tabControl", $aTabs);
$tabControl->Begin();
?>

<?
$tabControl->BeginNextTab();
?>

	<?if ($ID > 0):?>
		<tr>
			<td width="40%">ID:</td>
			<td width="60%"><?= $ID ?></td>
		</tr>
	<?endif;?>
	<tr class="adm-detail-required-field">
		<td width="40%"><?= GetMessage("CVAT_NAME") ?>:</td>
		<td width="60%"><input type="text" name="NAME" value="<?=$str_NAME?>" size="30" /></td>
	</tr>
	<tr class="adm-detail-required-field">
		<td><?= GetMessage("CVAT_RATE") ?>:</td>
		<td>
			<input type="text" name="RATE" value="<?=$str_RATE?>" size="10" />%
		</td>
	</tr>
	<tr>
		<td width="40%"><?= GetMessage("CVAT_ACTIVE") ?>:</td>
		<td width="60%">
			<input type="checkbox" name="ACTIVE" value="Y"<?if ($str_ACTIVE=="Y") echo " checked"?> />
		</td>
	</tr>
	<tr>
		<td width="40%"><?= GetMessage("CVAT_SORT") ?>:</td>
		<td width="60%">
			<input type="text" name="C_SORT" value="<?=$str_C_SORT?>" size="5" />
		</td>
	</tr>
<?
$tabControl->EndTab();

$tabControl->Buttons(
	array(
		"disabled" => $bReadOnly,
		"back_url" => "/bitrix/admin/cat_vat_admin.php?lang=".LANG."&".GetFilterParams("filter_", false)
	)
);
$tabControl->End();
?>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");?>