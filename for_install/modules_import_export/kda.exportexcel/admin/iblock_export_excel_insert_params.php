<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
use Bitrix\Main\Loader,
Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$moduleId = 'kda.exportexcel';
Loader::includeModule('iblock');
Loader::includeModule($moduleId);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$code = $_REQUEST['code'];

if($_POST['action']=='save')
{
	$APPLICATION->RestartBuffer();
	ob_end_clean();

	$retParams = array();
	if($code=='DATE')
	{
		$dateFormat = $_POST['DATE_FORMAT'];
		if(strlen($dateFormat)==0) $dateFormat = $_POST['DATE_FORMAT_ALT'];
		$retParams['val'] = '{DATE_'.preg_replace('/\s+/Uis', '_', $dateFormat).'}';
	}
	elseif($code=='CURRENCY_RATE')
	{
		$retParams['val'] = '{RATE_'.htmlspecialcharsex($_POST['CURRENCY_TYPE']).'.'.htmlspecialcharsex($_POST['CURRENCY']).'}';
	}
	echo '<script>EList.SetAddTextVal('.CUtil::PhpToJSObject($retParams).')</script>';
	die();
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data">
	<input type="hidden" name="action" value="save">

	<table width="100%">
	
		<?
		if($code=='DATE')
		{
			$prop = CIBlockParameters::GetDateFormat('', "ADDITIONAL_SETTINGS");
		?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo Loc::getMessage("KDA_EE_IP_DATE_FORMAT");?>:</td>
				<td class="adm-detail-content-cell-r">
					<?
					if(intval($prop["SIZE"])<=0)
						$prop["SIZE"] = 1;

					$res .= '<select name="DATE_FORMAT" size="'.$prop["SIZE"].'">';

					if(!is_array($prop["VALUES"]))
						$prop["VALUES"] = Array();

					if($prop['ADDITIONAL_VALUES']!=='N')
						$res .= '<option value="">'.Loc::getMessage("KDA_EE_IP_PROP_OTHER").'</option>';
					foreach($prop["VALUES"] as $v_id=>$v_name)
					{
						$v_name = ToLower(str_replace(2007, 2017, $v_name));
						$res .= '<option value="'.htmlspecialcharsbx($v_id).'"'.($v_id==$prop["DEFAULT"] ? ' selected' : '').'>'.htmlspecialcharsbx($v_name).'</option>';
					}
					$res .= '</select>';
					if($prop['ADDITIONAL_VALUES']!=='N')
					{
						$res .= '<br>';
						$res .= '<input type="text" name="DATE_FORMAT_ALT" value="">';
					}
					
					echo $res;
					?>					
				</td>
			</tr>
		<?
		}
		elseif($code=='CURRENCY_RATE')
		{
			$bCurrency = Loader::includeModule("currency");
			$arCurrency = array();
			if($bCurrency)
			{
				$lcur = CCurrency::GetList(($by="sort"), ($order1="asc"), LANGUAGE_ID);
				while($arr = $lcur->Fetch())
				{
					if($arr['BASE']=='Y') continue;
					$arCurrency[$arr['CURRENCY']] = (strlen(trim($arr['FULL_NAME'])) > 0 ? $arr['FULL_NAME'] : $arr['CURRENCY']);
				}
			}
			if(empty($arCurrency))
			{
				$arCurrency = array(
					'USD'=>Loc::getMessage("KDA_EE_IP_CURRENCY_USD"),
					'EUR'=>Loc::getMessage("KDA_EE_IP_CURRENCY_EUR")
				);
			}
		?>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo Loc::getMessage("KDA_EE_IP_CURRENCY");?>:</td>
				<td class="adm-detail-content-cell-r">
					<select name="CURRENCY">
					<?
					foreach($arCurrency as $k=>$v)
					{
						echo '<option value="'.htmlspecialcharsex($k).'">'.$v.'</option>';
					}
					?>
					</select>				
				</td>
			</tr>
			<tr>
				<td class="adm-detail-content-cell-l"><?echo Loc::getMessage("KDA_EE_IP_CURRENCY_TYPE");?>:</td>
				<td class="adm-detail-content-cell-r">
					<select name="CURRENCY_TYPE">
						<?if($bCurrency){?><option value="SITE"><?echo Loc::getMessage("KDA_EE_IP_CURRENCY_TYPE_SITE");?></option><?}?>
						<option value="CBR"><?echo Loc::getMessage("KDA_EE_IP_CURRENCY_TYPE_CBR");?></option>
					</select>				
				</td>
			</tr>
		<?}?>
		
	</table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>