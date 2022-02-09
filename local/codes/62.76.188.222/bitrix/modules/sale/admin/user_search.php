<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$saleMainPermissions = $APPLICATION->GetGroupRight("main");
if($saleMainPermissions < "R")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);

InitSorting();
$strError = "";

/***************************************************************************

***************************************************************************/

function CheckFilter() 
{
	global $strError, $FilterArr;
	reset($FilterArr); foreach ($FilterArr as $f) global $$f; 

	$str = "";

	if (strlen(trim($find_timestamp_1))>0 || strlen(trim($find_timestamp_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_timestamp_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_timestamp_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_timestamp_1))>0) 
			$str.= GetMessage("SALE_WRONG_TIMESTAMP_FROM")."<br>";
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_timestamp_2))>0) 
			$str.= GetMessage("SALE_WRONG_TIMESTAMP_TILL")."<br>";
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$str.= GetMessage("SALE_FROM_TILL_TIMESTAMP")."<br>";
	}

	if (strlen(trim($find_last_login_1))>0 || strlen(trim($find_last_login_2))>0)
	{
		$date_1_ok = false;
		$date1_stm = MkDateTime(FmtDate($find_last_login_1,"D.M.Y"),"d.m.Y");
		$date2_stm = MkDateTime(FmtDate($find_last_login_2,"D.M.Y")." 23:59","d.m.Y H:i");
		if (!$date1_stm && strlen(trim($find_last_login_1))>0) 
			$str.= GetMessage("SALE_WRONG_LAST_LOGIN_FROM")."<br>";
		else $date_1_ok = true;
		if (!$date2_stm && strlen(trim($find_last_login_2))>0) 
			$str.= GetMessage("SALE_WRONG_LAST_LOGIN_TILL")."<br>";
		elseif ($date_1_ok && $date2_stm <= $date1_stm && strlen($date2_stm)>0)
			$str.= GetMessage("SALE_FROM_TILL_LAST_LOGIN")."<br>";
	}
	
	$strError .= $str;
	if (strlen($str)>0) return false; else return true;

}

/***************************************************************************
						GET | POST
****************************************************************************/
$form_name = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $form_name);
$field_name = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $field_name);
$alt_name = preg_replace("/[^a-z0-9_\\[\\]:]/i", "", $alt_name);

if (strlen($form_name)<=0)
	$form_name = "form1";
if (strlen($field_name)<=0)
	$field_name = "USER_ID";
if (strlen($alt_name)<=0)
	$alt_name = "USER_ID_alt";

$FilterArr = Array(
	"find_id", 
	"find_timestamp_1",
	"find_timestamp_2",
	"find_last_login_1",
	"find_last_login_2",
	"find_active",
	"find_login",
	"find_name", 
	"find_email", 
	"find_keywords",
	"find_group_id"
	);
if (strlen($set_filter)>0)
	InitFilterEx($FilterArr,"USER_SEARCH","set"); 
else
	InitFilterEx($FilterArr,"USER_SEARCH","get");
if (strlen($del_filter)>0)
	DelFilterEx($FilterArr,"USER_SEARCH");
if (CheckFilter())
{
	$arFilter = Array(
		"ID"			=> $find_id,
		"TIMESTAMP_1"	=> $find_timestamp_1,
		"TIMESTAMP_2"	=> $find_timestamp_2,
		"LAST_LOGIN_1"	=> $find_last_login_1,
		"LAST_LOGIN_2"	=> $find_last_login_2,
		"ACTIVE"		=> $find_active,
		"LOGIN"			=> $find_login,
		"NAME"			=> $find_name,
		"EMAIL"			=> $find_email,
		"KEYWORDS"		=> $find_keywords,
		"GROUPS_ID"		=> $find_group_id
		);
}
$rsUsers = CUser::GetList($by, $order, $arFilter);
$is_filtered = $rsUsers->is_filtered;

/***************************************************************************
							HTML
****************************************************************************/

$APPLICATION->SetTitle(GetMessage("SALE_PAGE_TITLE"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php")
?>
<script language="JavaScript">
<!--
function SetValue(id, name)
{
	var el = eval("window.opener.document.<?= $form_name ?>.<?= $field_name ?>");
	if (el)
		el.value = id;
	el = window.opener.document.getElementById("<?= $alt_name ?>");
	if (el)
		el.innerHTML = name;
	window.close();
}
//-->
</script>

<br>
<?echo ShowError($strError);?>

<?echo BeginFilter("USER_LIST", $is_filtered);?>

<form name="form1" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
<tr> 
	<td class="tablebody"><font class="tablefieldtext"><?echo GetMessage("SALE_F_LOGIN")?></font></td>
	<td class="tablebody"><input class="typeinput" type="text" name="find_login" size="47" value="<?echo htmlspecialcharsbx($find_login)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr> 
	<td class="tablebody"><font class="tablefieldtext"><?echo GetMessage("SALE_F_ID")?></font></td>
	<td class="tablebody"><input class="typeinput" type="text" name="find_id" size="47" value="<?echo htmlspecialcharsbx($find_id)?>"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td class="tablebody" width="0%" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_TIMESTAMP")." (".CLang::GetDateFormat("SHORT")."):"?></font></td>
	<td class="tablebody" width="0%" nowrap><font class="tablefieldtext"><?echo CalendarPeriod("find_timestamp_1", htmlspecialcharsbx($find_timestamp_1), "find_timestamp_2", htmlspecialcharsbx($find_timestamp_2), "form1","Y")?></font></td>
</tr>
<tr>
	<td class="tablebody" width="0%" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_LAST_LOGIN")." (".CLang::GetDateFormat("SHORT")."):"?></font></td>
	<td class="tablebody" width="0%" nowrap><font class="tablefieldtext"><?echo CalendarPeriod("find_last_login_1", htmlspecialcharsbx($find_last_login_1), "find_last_login_2", htmlspecialcharsbx($find_last_login_2), "form1","Y")?></font></td>
</tr>
<tr>
	<td class="tablebody" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_ACTIVE")?></font></td>
	<td class="tablebody" nowrap><?
		$arr = array("reference"=>array(GetMessage("SALE_YES"), GetMessage("SALE_NO")), "reference_id"=>array("Y","N"));
		echo SelectBoxFromArray("find_active", $arr, htmlspecialcharsbx($find_active), GetMessage('SALE_ALL'));
		?>
	</td>
</tr>
<tr>
	<td class="tablebody" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_EMAIL")?></font></td>
	<td class="tablebody" nowrap><input class="typeinput" type="text" name="find_email" value="<?echo htmlspecialcharsbx($find_email)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td class="tablebody" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_NAME")?></font></td>
	<td class="tablebody" nowrap><input class="typeinput" type="text" name="find_name" value="<?echo htmlspecialcharsbx($find_name)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr>
	<td class="tablebody" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_KEYWORDS")?></font></td>
	<td class="tablebody" nowrap><input class="typeinput" type="text" name="find_keywords" value="<?echo htmlspecialcharsbx($find_keywords)?>" size="47"><?=ShowFilterLogicHelp()?></td>
</tr>
<tr valign="top">
	<td class="tablebody" nowrap><font class="tablefieldtext"><?echo GetMessage("SALE_F_GROUP")?></font></td>
	<td class="tablebody" nowrap><?
	$z = CGroup::GetDropDownList("AND ID!=2");
	echo SelectBoxM("find_group_id[]", $z, $find_group_id, "", false, 10);
	?></td>
</tr>
<tr>
	<td colspan="2" align="right" nowrap class="tablebody">
		<table border="0" cellspacing="0" cellpadding="0" width="100%">
			<tr>
				<td width="0%"><font class="tablebodytext">
				<input type="hidden" name="form_name" value="<?echo htmlspecialcharsbx($form_name)?>">
				<input type="hidden" name="field_name" value="<?echo htmlspecialcharsbx($field_name)?>">
				<input type="hidden" name="alt_name" value="<?echo htmlspecialcharsbx($alt_name)?>">
				<input type="hidden" name="lang" value="<?echo LANG?>">
				<input type="hidden" name="set_filter" value="Y">
				<input class="button" type="submit" name="set_filter" value="<?echo GetMessage("SALE_F_SET_FILTER")?>"></font></td>
				<td width="0%"><font class="tablebodytext">&nbsp;</font></td>
				<td width="100%"><font class="tablebodytext"><input class="button" type="submit" name="del_filter" value="<?echo GetMessage("SALE_F_DEL_FILTER")?>"></font></td>
				<td width="0%"><?ShowAddFavorite(false,"set_filter","main")?></td>
			</tr>
		</table>
	</td>
</tr>
</form>
<?echo EndFilter();?>

<p><?$rsUsers->NavStart(50); echo $rsUsers->NavPrint(GetMessage("SALE_PAGES"))?></p>
<table border="0" cellspacing="1" cellpadding="2" width="100%">
	<tr>
		<td valign="top" align="center" class="tablehead1" nowrap><font class="tableheadtext">ID<br><?echo SortingEx("id")?></font></td>
		<td valign="top" align="center" class="tablehead2" nowrap><font class="tableheadtext"><?echo GetMessage('SALE_TIMESTAMP')?><br><?echo SortingEx("timestamp_x")?></font></td>
		<td valign="top" align="center" class="tablehead2" nowrap><font class="tableheadtext"><?echo GetMessage('SALE_ACTIVE')?><br><?echo SortingEx("active")?></font></td>
		<td valign="top" align="center" class="tablehead2" nowrap><font class="tableheadtext"><?echo GetMessage('SALE_LOGIN')?><br><?echo SortingEx("login")?></font></td>
		<td valign="top" align="center" class="tablehead2" width="0%" nowrap>
			<table border="0" width="50%" cellspacing="0" cellpadding="0">
				<tr>
					<td nowrap><font class="tableheadtext"><?echo GetMessage("SALE_NAME")?></font></td>
					<td><?=SortingEx("name")?></td>
				</tr>
				<tr>
					<td nowrap><font class="tableheadtext"><? echo GetMessage("SALE_LAST_NAME")?></font></td>
					<td><?=SortingEx("last_name")?></td>
				</tr>
			</table></td>
		<td valign="top" align="center" class="tablehead2" nowrap><font class="tableheadtext"><?echo GetMessage('SALE_EMAIL')?><br><?echo SortingEx("email")?></font></td>
		<td valign="top" align="center" class="tablehead3" nowrap><font class="tableheadtext"><?echo GetMessage('SALE_ACTION')?></font>
		</td>
	</tr>
	<?
	while($rsUsers->NavNext(true, "f_")) :
	?>
	<tr valign="top">
		<td align="center" class="tablebody1"><input type="hidden" name="USER_ID[]" value="<?echo $f_ID?>"><font class="tablebodytext"><?echo $f_ID?></font>
		</td>
		<td align="center" class="tablebody2" nowrap><font class="tablebodytext"><?echo str_replace(" ", "<br>", $f_TIMESTAMP_X)?></b>&nbsp;</font></td>
		<td align="center" class="tablebody2"><font class="tablebodytext"><?echo ($f_ACTIVE=="Y" ? GetMessage('SALE_YES') : GetMessage('SALE_NO'));?></font></td>
		<td class="tablebody2"><font class="tablebodytext"><?echo $f_LOGIN?></font></td>
		<td class="tablebody2"><font class="tablebodytext"><?echo $f_NAME?><br><?echo $f_LAST_NAME?></font></td>
		<td class="tablebody2"><font class="tablebodytext"><?echo TxtToHtml($f_EMAIL)?></font></td>
		<td class="tablebody3" nowrap><input class="button" type="button" onClick="SetValue('<?= $f_ID ?>', '<?= str_replace("'", "\'", str_replace("\\", "\\\\", htmlspecialcharsbx($f_NAME.((strlen($f_NAME)<=0 || strlen($f_LAST_NAME)<=0) ? "" : " ").$f_LAST_NAME." (".$f_LOGIN.")"))) ?>');" value="<?echo GetMessage("SALE_SELECT")?>"></td>
	</tr>
	<?endwhile;?>
	<tr valign="top">
		<td class="tablebody4 selectedbody" width="0%" colspan="8"><font class="tablebodytext"><?echo GetMessage("SALE_TOTAL")?>&nbsp;<?echo $rsUsers->SelectedRowsCount()?></font></td>
	</tr>
</table>
<p><?echo $rsUsers->NavPrint(GetMessage("SALE_PAGES"))?></p>
<div align="left">
<input class="button" type="button" onClick="window.close()" value="<?echo GetMessage("SALE_CLOSE")?>"></div>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php")?>