<?
/**
 * Company developer: ALTASIB
 * Developer: adumnov
 * Site: http://www.altasib.ru
 * E-mail: dev@altasib.ru
 * @copyright (c) 2006-2017 ALTASIB
 */

$module_id = 'altasib.feedback';

IncludeModuleLangFile(__FILE__);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/altasib.feedback/include.php');
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");

function ShowParamsHTMLByArray($module_id, $arParams)
{
	foreach($arParams as $Option)
	{
		__AdmSettingsDrawRow($module_id, $Option);
	}
}

$arAllOptions = array(
	'enabled' => Array(
		Array('ALX_COMMON_CRM', GetMessage('ALX_COMMON_CRM'), 'Y', array('checkbox'))
	),
	'reCAPTCHA' => Array(
		Array('ALX_RECAPTCHA_SITE_KEY', GetMessage('ALX_FEEDBACK_SITE_KEY'), '', array('text', 50)),
		Array('ALX_RECAPTCHA_SECRET_KEY', GetMessage('ALX_FEEDBACK_SECRET_KEY'), '', array('text', 50)),
	),
);
$aTabs = array(
	array('DIV' => 'edit1', 'TAB' => GetMessage('MAIN_TAB_SET'), 'TITLE' => GetMessage('MAIN_TAB_TITLE_SET')),
);

$dbSites = CSite::GetList($by="sort", $order="desc", Array());

while($arSite = $dbSites->GetNext())
{
	$arAllOptions[$arSite['LID']]['reCAPTCHA'] = Array(
		Array('ALX_RECAPTCHA_SITE_KEY'.'_'.$arSite['LID'], GetMessage('ALX_FEEDBACK_SITE_KEY'), '', array('text', 50)),
		Array('ALX_RECAPTCHA_SECRET_KEY'.'_'.$arSite['LID'], GetMessage('ALX_FEEDBACK_SECRET_KEY'), '', array('text', 50)),
	);
}

//Restore defaults
if ($_SERVER['REQUEST_METHOD']=='GET' && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
	COption::RemoveOption($module_id);
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);

$siteK = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SITE_KEY', '');
$secretK = COption::GetOptionString('altasib.feedback', 'ALX_RECAPTCHA_SECRET_KEY', '');

//Save options
if($_POST["Update"] && strlen($Update)>0 && check_bitrix_sessid())
{
	foreach($arAllOptions as $aOptGroup)
	{
		foreach($aOptGroup as $key => $option)
		{
			if($key !== "reCAPTCHA")
			{
				__AdmSettingsSaveOption($module_id, $option);
			}
			else
			{
				foreach($option as $aSiteOption)
				{
					__AdmSettingsSaveOption($module_id, $aSiteOption);
				}
			}
		}
	}

	if($_POST['ALX_COMMON_CRM']!='Y')
	{
		COption::SetOptionString("altasib.feedback", "ALX_COMMON_CRM",'N');
	}
}
?>
<?
CJSCore::Init(array("jquery"));
?>
<form method='POST' action='<?echo $APPLICATION->GetCurPage()?>?mid=<?=htmlspecialchars($mid)?>&lang=<?=LANGUAGE_ID?>'>
<?=bitrix_sessid_post();?>
<?$tabControl->Begin();?>

<?$tabControl->BeginNextTab();?>
	<tr>
		<td colspan="2">
			<div style='background-color: #fff; padding: 0; border-top: 1px solid #8E8E8E; border-bottom: 1px solid #8E8E8E; margin-bottom: 15px;'>
				<div style='background-color: #8E8E8E; height: 30px; padding: 7px; border: 1px solid #fff'>
					<a href='http://www.is-market.ru?param=cl' target='_blank'>
						<img src='/bitrix/images/altasib.feedback/is-market.gif' style='float: left; margin-right: 15px;' border='0' />
					</a>
					<div style='margin: 13px 0px 0px 0px'>
						<a href='http://www.is-market.ru?param=cl' target='_blank' style='color: #fff; font-size: 10px; text-decoration: none'><?=GetMessage('ALTASIB_IS')?></a>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<?
	$all = COption::GetOptionString('altasib.feedback', 'ALX_COMMON_CRM', 'Y');
	if(isset($_REQUEST["msite"]))
	{
		$all = $_REQUEST["msite"];
	}

	?>
	<tr>
		<td valign='top' width='50%' class='field-name'><label for='ALX_COMMON_CRM'><?=GetMessage('ALX_COMMON_CRM')?><?=$all == 'Y' ? GetMessage('ALX_COMMON_CRM_AFTER_UNCHECK') : GetMessage('ALX_COMMON_CRM_AFTER_CHECK')?></label></td>
		<td valign='middle' width='50%'>
			<input type='checkbox' id='ALX_COMMON_CRM' name='ALX_COMMON_CRM' value='<?=$all?>' <?=$all == 'Y' ? ' checked' : ''?> onChange = 'altasib_func()'>
		</td>
	</tr>
	<?if ($all != 'N'):?>
		<tr class='heading'>
			<td colspan='2' style="font-size:unset; height:10px; background-color:#dbebe7"><?=GetMessage('ALX_RECAPTCHA_SUB')?></td>
		</tr>
		<?ShowParamsHTMLByArray($module_id, $arAllOptions['reCAPTCHA']);?>
	<?else:?>
		<?$dbSites = CSite::GetList($by="sort", $order="desc", Array());?>
		<?while($arSite = $dbSites->GetNext()):?>
			<tr class='heading'>
				<td colspan='2' style="font-size:larger;"><?=GetMessage('ALX_FEEDBACK_FOR_SITE').$arSite['LID']?></td>
			</tr>
			<tr class='heading'>
				<td colspan='2' style="font-size:unset; height:10px; background-color:#dbebe7"><?=GetMessage('ALX_RECAPTCHA_SUB')?></td>
			</tr>
			<?ShowParamsHTMLByArray($module_id, $arAllOptions[$arSite['LID']]['reCAPTCHA']);?>
		<?endwhile?>
	<?endif;?>

<style>
#altasib_description_full{
	display:none;
	transition:height 250ms;
}
#altasib_description_close_btn{
	display:none;
}
.altasib_description_open_text{
	border-bottom:1px solid;
	color:#2276cc !important;
	cursor:pointer;
	transition:color 0.3s linear 0s;
}
.altasib_events th{
	background:#dddddd none repeat scroll 0 0;
	font-style:normal;
	margin:2px;
	padding:3px 0.5em;
	text-align:left;
	vertical-align:middle;
}
</style>
<script language="JavaScript">
$(function(){
	var alx_obtn = $('#altasib_description_open_btn'),
	alx_cbtn = $('#altasib_description_close_btn'),
	full = $('#altasib_description_full');

	alx_obtn.click(function(){
		full.show(175);
		$(this).hide();
		alx_cbtn.show();
	});

	alx_cbtn.click(function(){
		full.hide(175);
		$(this).hide();
		alx_obtn.show();
	});
});
</script>
<?$tabControl->Buttons();?>
<script language='JavaScript'>
function RestoreDefaults()
{
	if(confirm('<?echo AddSlashes(GetMessage('MAIN_HINT_RESTORE_DEFAULTS_WARNING'))?>'))
		window.location = '<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANG?>&mid=<?echo urlencode($mid)?>&<?=bitrix_sessid_get()?>';
}
</script>
	<input type='submit' <?if(!$USER->IsAdmin())echo ' disabled';?> name='Update' value='<?echo GetMessage('BUTTON_SAVE')?>' class="adm-btn-save">
	<input type='reset' <?if(!$USER->IsAdmin())echo ' disabled';?> name='reset' value='<?echo GetMessage('BUTTON_RESET')?>' onClick = 'window.location.reload()'>
	<input type='button' <?if(!$USER->IsAdmin())echo ' disabled';?> title='<?echo GetMessage('BUTTON_DEF')?>' OnClick='RestoreDefaults();' value='<?echo GetMessage('BUTTON_DEF')?>'>
<?$tabControl->End();?>
</form>
<script type="text/javascript" >
function altasib_func()
{
	var mst = document.getElementById('ALX_COMMON_CRM').checked ? 'Y' : 'N';

	if(confirm('<?=AddSlashes(GetMessage('ON_CHANGE_COMMON_SETTS_WARNING'))?>')){
		document.getElementById('ALX_COMMON_CRM').value = mst;
		window.location = '<?=$APPLICATION->GetCurPage()?>?msite='+mst+'&lang=<?=LANG?>&mid=<?=urlencode($mid)?>&<?=bitrix_sessid_get()?>';
	} else
		document.getElementById('ALX_COMMON_CRM').checked = !document.getElementById('ALX_COMMON_CRM').checked;
}
</script>