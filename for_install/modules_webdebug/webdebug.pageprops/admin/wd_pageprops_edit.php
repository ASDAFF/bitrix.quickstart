<?
$ModuleID = 'webdebug.pageprops';
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$ModuleID.'/install/demo.php');
IncludeModuleLangFile(__FILE__);

if (webdebug_pageprops_demo_expired()) {
	webdebug_pageprops_show_demo();
	die();
}

$PropType = $_REQUEST['PROPERTY_TYPE'];
$PropCode = $_REQUEST['prop_code'];
$PropSite = $_REQUEST['prop_site'];
if ($PropSite=='com') {
	$PropSite = '';
}

// Get current property data
$arFilter = CWD_Pageprops::GetFilter($PropCode, $PropSite);
$resCurrentProp = CWD_Pageprops::GetList(false,$arFilter);
$arCurrentProp = $resCurrentProp->GetNext();
if ($arCurrentProp===false) {
	$arCurrentProp = array();
}

// Get types
$arPropTypes = CWD_Pageprops::GetTypes();

if($_REQUEST['save']=='Y' && check_bitrix_sessid('wd_pageprops_sessid')) {
	$bSettingsSaved = CWD_Pageprops::SaveSettings($PropCode, $PropSite, $PropType, $_POST);
	if (!$bSettingsSaved) {
		print '#WD_PAGEPROPS_SAVE_ERROR#';
	}
	die();
}

if ($_GET['get_prop_settings']=='Y') {
	print CWD_Pageprops::ShowSettings($PropCode, $_GET['prop_type']);
	die();
}

if (isset($arPropTypes[$arCurrentProp['TYPE']])) {
	$PropType = $arCurrentProp['TYPE'];
}

?>

<form action="<?=$APPLICATION->GetCurPage(false);?>" class="wd_pageprops_settings_form" id="wd_pageprops_settings_form" method="post">
	<?=bitrix_sessid_post('wd_pageprops_sessid');?>
	<input type="hidden" name="anticache" value="<?=rand(100000000,999999999)?>" />
	<?foreach($_GET as $Param => $Value):?>
		<input type="hidden" name="<?=$Param;?>" value="<?=$Value?>" />
	<?endforeach?>
	<div>
		<div class="wd_pageprops_select_type">
			<select name="PROPERTY_TYPE">
				<option value="DEFAULT"><?=GetMessage('WD_PAGEPROPS_TYPE_DEFAULT');?></option>
				<?foreach($arPropTypes as $arPropType):?>
					<option value="<?=$arPropType['CODE'];?>"<?if($PropType==$arPropType['CODE']):?> selected<?endif?>><?=$arPropType['NAME'];?></option>
				<?endforeach?>
			</select>
		</div>
		<br/>
		<div class="wd_pageprops_prop_settings"></div>
	</div>
</form>

<script>
$('#wd_pageprops_settings_form .wd_pageprops_select_type select').change(function(){
	$.ajax({
		url: '<?=$APPLICATION->GetCurPage(false);?>',
		type: 'GET',
		data: 'get_prop_settings=Y&prop_type='+$(this).val()+'&lang=<?=htmlspecialchars($_GET['lang']);?>&prop_site=<?=$PropSite;?>&prop_code=<?=htmlspecialchars($PropCode);?>&anticache='+Math.random(),
		success: function(HTML) {
			$('#wd_pageprops_settings_form .wd_pageprops_prop_settings').html(HTML);
		}
	});
}).change();
</script>

<?
require_once ($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin_after.php");
?>