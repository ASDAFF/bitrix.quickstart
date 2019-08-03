<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/iblock/prolog.php");
$moduleId = 'esol.importxml';
CModule::IncludeModule('iblock');
CModule::IncludeModule($moduleId);
IncludeModuleLangFile(__FILE__);

$MODULE_RIGHT = $APPLICATION->GetGroupRight($moduleId);
if($MODULE_RIGHT < "W") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

$arGet = $_GET;
$INPUT_ID = $arGet['INPUT_ID'];
$IBLOCK_ID = (int)$arGet['IBLOCK_ID'];
$isOffers = false;
if(strpos($INPUT_ID, 'OFFER_')===0)
{
	$IBLOCK_ID = \Bitrix\EsolImportxml\Utils::GetOfferIblock($IBLOCK_ID);
	$isOffers = true;
}

if($_POST['action']=='save')
{	
	$APPLICATION->RestartBuffer();
	if(ob_get_contents()) ob_end_clean();
	
	echo '<script>';
	echo '$("#'.$INPUT_ID.'").val("'.(is_array($_POST['DEFAULTS']) ? base64_encode(serialize($_POST['DEFAULTS'])) : '').'");';
	echo 'BX.WindowManager.Get().Close();';
	echo '</script>';
	die();
}

if($OLDDEFAULTS) $DEFAULTS = unserialize(base64_decode($OLDDEFAULTS));
if(!is_array($DEFAULTS)) $DEFAULTS= array();


$arDefaultProps = array();
$dbRes = CIBlockProperty::GetList(array("sort" => "asc", "name" => "asc"), array("ACTIVE" => "Y", 'IBLOCK_ID'=>$IBLOCK_ID));
while($arr = $dbRes->Fetch())
{
	$arDefaultProps[$arr['ID']] = $arr;
}

$arDefaultCatFields = \Bitrix\EsolImportxml\FieldList::GetCatalogDefaultFields($IBLOCK_ID);
$arDefaultElFields = \Bitrix\EsolImportxml\FieldList::GetIblockElementDefaultFields();

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
?>
<form action="" method="post" enctype="multipart/form-data" name="field_settings">
	<input type="hidden" name="action" value="save">
	<table width="100%" class="esol-ix-list-settings">
		<col width="50%">
		<col width="50%">
		
		<tr class="heading">
			<td colspan="2">
				<?echo GetMessage(($isOffers ? "ESOL_IX_LIST_SETTING_PROPERTIES_DEFAULT_OFFER" : "ESOL_IX_LIST_SETTING_PROPERTIES_DEFAULT")); ?>
			</td>
		</tr>
		<?
		if(is_array($DEFAULTS))
		{
			foreach($DEFAULTS as $k=>$v)
			{
				if(isset($arDefaultProps[$k])) $fieldName = $arDefaultProps[$k]['NAME'];
				elseif(isset($arDefaultCatFields[$k])) $fieldName = $arDefaultCatFields[$k]['NAME'];
				else $fieldName = $arDefaultElFields[$k]['NAME'];
				?>
				<tr class="esol-ix-list-settings-defaults">
					<td class="adm-detail-content-cell-l"><?echo $fieldName;?>:</td>
					<td class="adm-detail-content-cell-r">
						<input type="text" name="DEFAULTS[<?echo $k;?>]" value="<?echo htmlspecialcharsex($v);?>">
						<a class="delete" href="javascript:void(0)" onclick="ESettings.RemoveDefaultProp(this);" title="<?echo GetMessage("ESOL_IX_LIST_SETTING_DELETE"); ?>"></a>
					</td>
				</tr>
				<?
			}
		}
		?>		
		<tr class="esol-ix-list-settings-defaults" style="display: none;">
			<td class="adm-detail-content-cell-l"></td>
			<td class="adm-detail-content-cell-r">
				<input type="text" name="empty" value="">
				<a class="delete" href="javascript:void(0)" onclick="ESettings.RemoveDefaultProp(this);" title="<?echo GetMessage("ESOL_IX_LIST_SETTING_DELETE"); ?>"></a>
			</td>
		</tr>
		<tr>
			<td colspan="2" class="esol-ix-chosen-td">
				<select name="prop_default" style="min-width: 200px;" class="esol-ix-chosen-multi" onchange="ESettings.AddDefaultProp(this)">
					<option value=""><?echo GetMessage('ESOL_IX_PLACEHOLDER_CHOOSE');?></option>
					<optgroup label="<?echo GetMessage('ESOL_IX_LIST_SETTING_ELEMENT');?>">
						<?
						foreach($arDefaultElFields as $elKey=>$elField)
						{
							echo '<option value="'.$elKey.'">'.$elField['NAME'].'</option>';
						}
						?>
					</optgroup>
					<?if(!empty($arDefaultProps)){?>
						<optgroup label="<?echo GetMessage('ESOL_IX_LIST_SETTING_PROPERTIES');?>">
							<?
							foreach($arDefaultProps as $prop)
							{
								echo '<option value="'.$prop['ID'].'">'.$prop['NAME'].'</option>';
							}
							?>
						</optgroup>
					<?}?>
					<?if(!empty($arDefaultCatFields)){?>
						<optgroup label="<?echo GetMessage('ESOL_IX_LIST_SETTING_CATALOG');?>">
							<?
							foreach($arDefaultCatFields as $cKey=>$cField)
							{
								echo '<option value="'.$cKey.'">'.$cField['NAME'].'</option>';
							}
							?>
						</optgroup>
					<?}?>
				</select>
			</td>
		</tr>		
	</table>
</form>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");?>