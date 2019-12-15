<?
$module_id = 'redsign.devfunc';
/** @global CMain $APPLICATION */
/** @global string $RestoreDefaults */
/** @global string $Update */

use Bitrix\Main\Loader;
use Bitrix\Main\SiteTable;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Diag\Debug;
use \Bitrix\Main\EventManager;

function wrapOptionLHE($inputName, $content = '', $divId = false)
{
	ob_start();
	$ar = array(
		'inputName' => $inputName,
		'height' => '160',
		'width' => '320',
		'content' => $content,
		'bResizable' => true,
		'bManualResize' => true,
		'bUseFileDialogs' => false,
		'bFloatingToolbar' => false,
		'bArisingToolbar' => false,
		'bAutoResize' => true,
		'bSaveOnBlur' => true,
		'toolbarConfig' => array(
			'Bold', 'Italic', 'Underline', 'Strike',
			'CreateLink', 'DeleteLink',
			'Source', 'BackColor', 'ForeColor'
		)
	);

	if($divId)
		$ar['id'] = $divId;

	\Bitrix\Main\Loader::includeModule('fileman');

	$LHE = new CLightHTMLEditor;
	$LHE->Show($ar);
	$sVal = ob_get_contents();
	ob_end_clean();

	return $sVal;
}

$REDSIGN_DEVFUNC_RIGHT = $APPLICATION->GetGroupRight($module_id);
if ($REDSIGN_DEVFUNC_RIGHT>='R') :

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/options.php');
Loc::loadMessages(__FILE__);

$defaultValue = array('-' => GetMessage('REDSIGN.DEVFUNC.OPTIONS.UNDEFINED'));

Loader::includeModule($module_id);

$siteList = array();
$siteIterator = SiteTable::getList(array(
	'select' => array('LID', 'NAME'),
	'order' => array('SORT' => 'ASC')
));
while ($oneSite = $siteIterator->fetch())
{
	$siteList[] = array('ID' => $oneSite['LID'], 'NAME' => $oneSite['NAME']);
}
unset($oneSite, $siteIterator);

$siteCount = count($siteList);

$bWasUpdated = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && strlen($RestoreDefaults)>0 && $REDSIGN_DEVFUNC_RIGHT=='W' && check_bitrix_sessid())
{
	$bWasUpdated = true;

	COption::RemoveOption($module_id);
	$z = CGroup::GetList($v1='id',$v2='asc', array('ACTIVE' => 'Y', 'ADMIN' => 'N'));
	while($zr = $z->Fetch())
		$APPLICATION->DelGroupRight($module_id, array($zr['ID']));
}

$arAllOptions =
	array(
    );

$aTabs = array(
	array('DIV' => 'redsign_sline_settings', 'TAB' => Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.SETTINGS'), 'ICON' => 'settings', 'TITLE' => Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.SETTINGS')),
);

$tabControl = new CAdminTabControl('tabControl', $aTabs);

$strWarning = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (strlen($Update) > 0 || strlen($Apply) > 0) && $REDSIGN_DEVFUNC_RIGHT == 'W' && check_bitrix_sessid())
{
    $bWasUpdated = true;

    for ($i = 0, $intCount = count($arAllOptions); $i < $intCount; $i++)
    {
        if(!empty($arAllOptions[$i]))
        {
            $name = $arAllOptions[$i][0];
            $val = ${$name};
            if ($arAllOptions[$i][3][0]=='checkbox' && $val!='Y')
                $val = 'N';

            COption::SetOptionString($module_id, $name, $val, $arAllOptions[$i][1]);
        }
    }
	
	$bReplaceLocationRegion = false;
	$bUseLocationRegion = false;
	if (is_array($siteList) && count($siteList) > 0) {
		foreach ($siteList as $arSite) {
			$siteId = $arSite["ID"];

			// REPLACE_LOCATION_REGION replace location in sale.order.ajax component
			$val = $REPLACE_LOCATION_REGION[$siteId] == 'Y'
				? 'Y'
				: 'N';
			Option::set($module_id, 'replace_location_region', $val, $siteId);
			if ($val == 'Y') {
				$bReplaceLocationRegion = true;
			}

			// USE_LOCATION_REGION
			$val = $USE_LOCATION_REGION[$siteId] == 'Y'
				? 'Y'
				: 'N';
			Option::set($module_id, 'use_location_region', $val, $siteId);
			if ($val == 'Y') {
				$bUseLocationRegion = true;
			}

			// LOCATION_REGION_TYPE
			// $val = $LOCATION_REGION_TYPE[$siteId];
			// Option::set($module_id, 'location_region_type', $val, $siteId);
			
			// LOCATION_REGION_IBLOCK_ID
			$val = intval($LOCATION_REGION_IBLOCK_ID[$siteId]) > 0
				? $LOCATION_REGION_IBLOCK_ID[$siteId]
				: null;
			Option::set($module_id, 'location_region_iblock_id', $val, $siteId);
			
			// DEFAULT_LOCATION_CODE
			$val = strlen($DEFAULT_LOCATION_CODE[$siteId]) > 0
				? $DEFAULT_LOCATION_CODE[$siteId]
				: '';
			Option::set($module_id, 'default_location_code', $val, $siteId);
			
			
			// FAKEPRICE_ACTIVE
			$val = $FAKEPRICE_ACTIVE[$siteId] == 'Y'
				? $FAKEPRICE_ACTIVE[$siteId]
				: 'N';
			Option::set($module_id, 'fakeprice_active', $val, $siteId);
			
			// PROPCODE_CML2LINK
			$val = strlen($PROPCODE_CML2LINK[$siteId]) > 0
				? $PROPCODE_CML2LINK[$siteId]
				: '';
			Option::set($module_id, 'propcode_cml2link', $val, $siteId);

			// PROPCODE_FAKEPRICE
			$val = strlen($PROPCODE_FAKEPRICE[$siteId]) > 0
				? $PROPCODE_FAKEPRICE[$siteId]
				: '';
			Option::set($module_id, 'propcode_fakeprice', $val, $siteId);
			
			// LOCATION_REGION_IBLOCK_ID
			$val = intval($PRICE_FOR_FAKE[$siteId]) > 0
				? $PRICE_FOR_FAKE[$siteId]
				: null;
			Option::set($module_id, 'price_for_fake', $val, $siteId);
			
			// NO_PHOTO_PATH
			$val = strlen($NO_PHOTO_PATH[$siteId]) > 0
				? $NO_PHOTO_PATH[$siteId]
				: '';
				
			$prev_no_photo_path = Option::get($module_id, 'no_photo_path', '', $siteId);
			Option::set($module_id, 'no_photo_path', $val, $siteId);
			
			if ($prev_no_photo_path != $val) {
				if ($val != '') {
					$arFile = CFile::MakeFileArray($_SERVER["DOCUMENT_ROOT"].$val);
					$fid = CFile::SaveFile($arFile, "redsign_devfunc_nophoto");
					Option::set($module_id, 'no_photo_fileid', $fid);
				} else {
					Option::set($module_id, 'no_photo_fileid', 0);
				}
			}
		}
	}
	
	if ($bUseLocationRegion) {

		EventManager::getInstance()->registerEventHandler(
		  'main',
		  'OnPageStart',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnPageStart'
		);
		
		EventManager::getInstance()->registerEventHandler(
		  'main',
		  'OnEndBufferContent',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnEndBufferContent'
		);
		
		EventManager::getInstance()->registerEventHandler(
		  'catalog',
		  'OnGetOptimalPrice',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnGetOptimalPrice'
		);

	} else {

		EventManager::getInstance()->unRegisterEventHandler(
		  'main',
		  'OnPageStart',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnPageStart'
		);
		
		EventManager::getInstance()->unRegisterEventHandler(
		  'main',
		  'OnEndBufferContent',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnEndBufferContent'
		);
		
		EventManager::getInstance()->unRegisterEventHandler(
		  'catalog',
		  'OnGetOptimalPrice',
		  $module_id,
		  '\Redsign\DevFunc\Sale\Location\Region',
		  'OnGetOptimalPrice'
		);

	}
}

if($strWarning != '')
	CAdminMessage::ShowMessage($strWarning);
elseif ($bWasUpdated)
{
	if(strlen($Update)>0 && strlen($_REQUEST['back_url_settings'])>0)
		LocalRedirect($_REQUEST['back_url_settings']);
	else
		LocalRedirect($APPLICATION->GetCurPage()."?mid=".$module_id."&lang=".LANGUAGE_ID."&back_url_settings=".urlencode($_REQUEST["back_url_settings"])."&".$tabControl->ActiveTabParam());
}


$arIBlock = array();
if (Loader::includeModule('iblock')) {
    $rsIBlock = CIBlock::GetList(array('SORT' => 'ASC'), array('ACTIVE' => 'Y'));
    while ($arr = $rsIBlock->Fetch()) {
        $arIBlock[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME'];
	}
    unset($arr, $rsIBlock);
}

if (Loader::includeModule('catalog')) {
    $arPrice = array();
    $rsPrice = CCatalogGroup::GetList($v1='sort',$v2='asc');
    while ($arr = $rsPrice->Fetch()) {
        $arPrice[$arr['ID']] = '['.$arr['ID'].'] '.$arr['NAME_LANG'];
    }
}

$tabControl = new CAdminTabControl('tabControl', $aTabs);
$tabControl->Begin();
?><script type="text/javascript">
function OnColorPicker(val,obj){
	document.getElementById(obj.oPar.id).value = val;
}
</script><?
?><form method="POST" action="<?echo $APPLICATION->GetCurPage()?>?mid=<?=$module_id?>&lang=<?=LANGUAGE_ID?>" name="opt_form">
<?$tabControl->BeginNextTab();?>
<?
/*
	<tr class="heading">
		<td colspan="3"><?=GetMessage('RSAL_COLOR_TABLE')?></td>
	</tr>
*/
?>
	<tr>
		<td colspan="3">
            <?
            $aTabs2 = Array();
			foreach($siteList as $val)
			{
				$aTabs2[] = Array('DIV'=>'personal'.$val['ID'], 'TAB' => '['.$val['ID'].'] '.htmlspecialcharsbx($val['NAME']), 'TITLE' => '['.htmlspecialcharsbx($val['ID']).'] '.htmlspecialcharsbx($val['NAME']));
			}
			$tabControl2 = new CAdminViewTabControl('tabControl2', $aTabs2);
			$tabControl2->Begin();
			foreach ($siteList as $val) {

				$tabControl2->BeginNextTab();
				?>
				<table cellspacing="5" cellpadding="0" border="0" width="100%" align="center">

					<tr class="heading">
						<td colspan="2"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_TITLE')?></td>
					</tr>
					<tr>
						<td align="right" width="49%"><label for="DEFAULT_LOCATION_CODE-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.DEFAULT_LOCATION_CODE')?>:</label></td>
						<td width="51%">
							<?php
							$sDefaultLocationCode = Option::get($module_id, 'default_location_code', null, $val['ID']);
							?>
							  <?php $APPLICATION->IncludeComponent(
								  "bitrix:sale.location.selector.search",
								  "",
								  Array(
									  "COMPONENT_TEMPLATE" => ".default",
									  "ID" => 'DEFAULT_LOCATION_CODE-'.$val['ID'],
									  "CODE" => $sDefaultLocationCode,
									  "INPUT_NAME" => 'DEFAULT_LOCATION_CODE['.$val['ID'].']',
									  "PROVIDE_LINK_BY" => "code",
									  "JS_CONTROL_GLOBAL_ID" => "",
									  "FILTER_BY_SITE" => "Y",
									  "SHOW_DEFAULT_LOCATIONS" => "Y",
									  "CACHE_TYPE" => "A",
									  "CACHE_TIME" => "36000000",
									  "FILTER_SITE_ID" => $val['ID'],
									  "INITIALIZE_BY_GLOBAL_EVENT" => "",
									  "SUPPRESS_ERRORS" => "N"
								  )
							  ); ?>
						</td>
					</tr>
					<tr>
						<?php $bReplaceLocationRegion = Option::get($module_id, 'replace_location_region', 'N', $val['ID']); ?>
						<td align="right" width="49%"><label for="REPLACE_LOCATION_REGION-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.REPLACE_LOCATION_REGION')?>:</label></td>
						<td width="51%"><input type="checkbox" name="REPLACE_LOCATION_REGION[<?=$val['ID']?>]" value="Y" id="REPLACE_LOCATION_REGION-<?=$val['ID']?>"<?if($bReplaceLocationRegion == 'Y') echo ' checked';?>></td>
					</tr>

					<?php if (Loader::includeModule('sale')): ?>
						<tr class="heading">
							<td colspan="2"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_REGION')?></td>
						</tr>
						<tr>
							<?php $bUseLocationRegion = Option::get($module_id, 'use_location_region', 'Y', $val['ID']); ?>
							<td align="right" width="49%"><label for="USE_LOCATION_REGION-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.USE_LOCATION_REGION')?>:</label></td>
							<td width="51%"><input type="checkbox" name="USE_LOCATION_REGION[<?=$val['ID']?>]" value="Y" id="USE_LOCATION_REGION-<?=$val['ID']?>"<?if($bUseLocationRegion == 'Y') echo ' checked';?>></td>
						</tr>

						<?php if ($bUseLocationRegion == 'Y'): ?>
	<?/*
							<tr>
								<?php
								$sLocationRegionType = Option::get($module_id, 'location_region_type', 'DOMAIN', $val['ID']);
								$arLocationRegionType = array(
									'DOMAIN' => Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_REGION_TYPE.DOMAIN'),
									'SUBDOMAIN' => Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_REGION_TYPE.SUBDOMAIN'),
								);
								?>
								<td align="right" width="49%"><label for="LOCATION_REGION_TYPE-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_REGION_TYPE')?>:</label></td>
								<td width="51%">
									<select name="LOCATION_REGION_TYPE[<?=$val['ID']?>]" id="LOCATION_REGION_TYPE-<?=$val['ID']?>">
										<?php foreach ($arLocationRegionType as $code => $name): ?>
											<option value="<?=$code?>"<?php if ($sLocationRegionType == $code): ?> selected<?php endif; ?>><?=$name?></option>
										<?php endforeach; ?>
										<?php unset($code, $name); ?>
									</select>
								</td>
							</tr>
	*/?>
							<tr>
								<?php
								$sLocationRegionIblockId = Option::get($module_id, 'location_region_iblock_id', null, $val['ID']);
								?>
								<td align="right" width="49%"><label for="LOCATION_REGION_IBLOCK_ID-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.LOCATION_REGION_IBLOCK_ID')?>:</label></td>
								<td width="51%">
									<select name="LOCATION_REGION_IBLOCK_ID[<?=$val['ID']?>]" id="LOCATION_REGION_IBLOCK_ID-<?=$val['ID']?>">
										<?php foreach ($defaultValue + $arIBlock as $iblockId => $iblockName): ?>
											<option value="<?=$iblockId?>"<?php if ($sLocationRegionIblockId == $iblockId): ?> selected<?php endif; ?>><?=$iblockName?></option>
										<?php endforeach; ?>
										<?php unset($iblockId, $iblockName); ?>
									</select>
								</td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>

					<tr class="heading">
						<td colspan="2"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.FAKEPRICE_OPTIONS')?></td>
					</tr>
					<tr>
						<?php $bFakepriceActive = Option::get($module_id, 'fakeprice_active', 'Y', $val['ID']); ?>
						<td align="right" width="49%"><label for="FAKEPRICE_ACTIVE-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.FAKEPRICE_ACTIVE')?>:</label></td>
						<td width="51%"><input type="checkbox" name="FAKEPRICE_ACTIVE[<?=$val['ID']?>]" value="Y" id="FAKEPRICE_ACTIVE-<?=$val['ID']?>"<?if($bFakepriceActive == 'Y') echo ' checked';?>></td>
					</tr>

					<?php if ($bFakepriceActive == 'Y'): ?>
						<tr>
							<?php $sPropcodeCml2link = Option::get($module_id, 'propcode_cml2link', 'CML2_LINK', $val['ID']); ?>
							<td align="right" width="49%"><label for="PROPCODE_CML2LINK-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.PROPCODE_CML2LINK')?>:</label></td>
							<td width="51%"><input type="text" name="PROPCODE_CML2LINK[<?=$val['ID']?>]" value="<?=$sPropcodeCml2link?>" id="PROPCODE_CML2LINK-<?=$val['ID']?>"></td>
						</tr>
						<tr>
							<?php $sPropcodeFakePrice = Option::get($module_id, 'propcode_fakeprice', 'PRICE', $val['ID']); ?>
							<td align="right" width="49%"><label for="PROPCODE_FAKEPRICE-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.PROPCODE_FAKEPRICE')?>:</label></td>
							<td width="51%"><input type="text" name="PROPCODE_FAKEPRICE[<?=$val['ID']?>]" value="<?=$sPropcodeFakePrice?>" id="PROPCODE_FAKEPRICE-<?=$val['ID']?>"></td>
						</tr>

						<?php if (is_array($arPrice) && count($arPrice) > 0): ?>
						<tr>
							<?php $sPriceForFake = Option::get($module_id, 'price_for_fake', '0', $val['ID']); ?>
							<td align="right" width="49%"><label for="PRICE_FOR_FAKE-<?=$val['ID']?>"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.PRICE_FOR_FAKE')?>:</label></td>
							<td width="51%">
								<select name="PRICE_FOR_FAKE[<?=$val['ID']?>]" id="PRICE_FOR_FAKE-<?=$val['ID']?>">
									<option value="0">-</option>
									<?php foreach ($arPrice as $priceID => $priceName): ?>
										<option value="<?=$priceID?>"<?php if ($sPriceForFake == $priceID): ?> selected<?php endif; ?>><?=$priceName?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<?php endif; ?>
					<?php endif; ?>

					<tr>
						<?php
						$no_photo_path = Option::get($module_id, 'no_photo_path', '', $val['ID']);
						$no_photo_fileid = Option::get($module_id, 'no_photo_fileid', 0, $val['ID']);
						?>
						<td align="right" width="49%"><?=Loc::getMessage('REDSIGN.DEVFUNC.OPTIONS.NO_PHOTO_PATH')?></td>
						<td width="51%"><input type="text" name="NO_PHOTO_PATH[<?=$val['ID']?>]" value="<?=$no_photo_path?>" />
							<input type="hidden" name="NO_PHOTO_FILEID" value="<?=$no_photo_fileid?>" />
							<input type="button" value="<?=Loc::getMessage("REDSIGN.DEVFUNC.OPTIONS.BTN_FILEDIALOG")?>" OnClick="BtnFileDialogOpenNoPhoto()">
							<?php
							CAdminFileDialog::ShowScript(
								Array(
									"event" => "BtnFileDialogOpenNoPhoto",
									"arResultDest" => array("FORM_NAME" => "redsign_devfunc_option", "FORM_ELEMENT_NAME" => "no_photo_path[".$val['ID']."]"),
									"arPath" => array("SITE" => SITE_ID, "PATH" => ""),
									"select" => 'F',// F - file only, D - folder only
									"operation" => 'O',// O - open, S - save
									"showUploadTab" => true,
									"showAddToMenuTab" => false,
									"fileFilter" => 'image',
									"allowAllFiles" => true,
									"SaveConfig" => true,
								)
							);
							?>
						</td>
					</tr>
				</table>
				<?php
			}
			$tabControl2->End();
			?>
		</td>
	</tr>

<?$tabControl->Buttons();?>
<script type="text/javascript">
function RestoreDefaults()
{
	if (confirm('<?echo addslashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>'))
		window.location = "<?echo $APPLICATION->GetCurPage()?>?RestoreDefaults=Y&lang=<?echo LANGUAGE_ID?>&mid=<?echo $module_id?>";
}
</script>
	<input type="hidden" name="siteTabControl_active_tab" value="<?=htmlspecialcharsbx($_REQUEST["siteTabControl_active_tab"])?>">
<?if($_REQUEST["back_url_settings"] <> ''):?>
	<input type="submit" name="Update" <?if ($REDSIGN_DEVFUNC_RIGHT<"W") echo "disabled" ?> value="<?=GetMessage("MAIN_SAVE")?>" title="<?=GetMessage("MAIN_OPT_SAVE_TITLE")?>">
<?endif?>
	<input type="submit" name="Apply" value="<?=GetMessage("MAIN_OPT_APPLY")?>" title="<?=GetMessage("MAIN_OPT_APPLY_TITLE")?>">
<?if($_REQUEST["back_url_settings"] <> ''):?>
	<input type="button" name="Cancel" value="<?=GetMessage("MAIN_OPT_CANCEL")?>" title="<?=GetMessage("MAIN_OPT_CANCEL_TITLE")?>" onclick="window.location='<?echo htmlspecialcharsbx(CUtil::addslashes($_REQUEST["back_url_settings"]))?>'">
	<input type="hidden" name="back_url_settings" value="<?=htmlspecialcharsbx($_REQUEST["back_url_settings"])?>">
<?endif?>
	<input type="submit" name="RestoreDefaults" <?if ($REDSIGN_DEVFUNC_RIGHT<"W") echo "disabled" ?> title="<?echo GetMessage("MAIN_HINT_RESTORE_DEFAULTS")?>" onclick="return confirm('<?echo AddSlashes(GetMessage("MAIN_HINT_RESTORE_DEFAULTS_WARNING"))?>')" value="<?echo GetMessage("MAIN_RESTORE_DEFAULTS")?>">
	<?=bitrix_sessid_post();?>
<?$tabControl->End();?>
</form>

<?
echo BeginNote();
echo GetMessage('REDSIGN.DEVFUNC.OPTIONS.REPLACE_LOCATION_REGION.NOTE');
echo EndNote();

endif;

