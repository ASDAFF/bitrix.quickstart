<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php"); 
IncludeModuleLangFile( __FILE__);
$APPLICATION->SetTitle(GetMessage("MCART_IMPORT_XLS_STEP_0"));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

?>
<form action="/bitrix/admin/mcart_xls_import.php" enctype="multipart/form-data" method="POST">
<?
global $DB;
$arrProfile = array();
$strSql = "SELECT id, name from mcart_xls";
		$ret =  $DB->Query($strSql, false);
while ($ar = $ret->GetNext())
	$arrProfile[$ar['id']] =$ar['name'];
	
?>
<p><input type="file" name='xls_file_name'></p>
</br>
<? if (!empty($arrProfile)):
$arrProfile[0] = GetMessage("NOT_LOAD_PROFILE");

?>
<h4><?=GetMessage("XLS_SELECT_PROFILE")?></h4>
			<select name = "xls_profile" >
				<?foreach ($arrProfile as $key=>$value):?>
				<option value="<?=$key?>"><?=$value." [".$key."] "?></option>
				<?endforeach?>
			</select>
			<input type="submit" name="del_prof" value="<?=GetMessage("MCART_DEL_PROFLE")?>">
<? endif; ?>			
</br>
<input type="submit" name="next_step" value="<?=GetMessage("NEXT_STEP")?>">
</form>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php"); ?>