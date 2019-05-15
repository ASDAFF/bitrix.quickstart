<?
IncludeModuleLangFile(__FILE__);

class CWD_Pageprop_FileSite extends CWD_PagepropsAll {
	CONST CODE = 'FILE_SITE';
	CONST NAME = 'Файл/папка на сайте';
	function GetName() {
		$Name = self::NAME;
		if (CWD_Pageprops::IsUtf8()) {
			$Name = $GLOBALS['APPLICATION']->ConvertCharset($Name, 'CP1251', 'UTF-8');
		}
		return $Name;
	}
	function GetCode() {
		return self::CODE;
	}
	function GetMessage($Item) {
		$arMess = array(
			'OPTION_COLUMN_PARAM' => 'Параметр',
			'OPTION_COLUMN_VALUE' => 'Значение',
			'OPTION_START_PATH' => 'Начальный путь',
			'OPTION_START_SITE' => 'Начальный сайт',
				'OPTION_START_SITE_EMPTY' => '--- по умолчанию ---',
			'OPTION_TYPE' => 'Тип выбираемого объекта (файл или папка)',
				'OPTION_TYPE_F' => 'файл',
				'OPTION_TYPE_D' => 'папка',
			'OPTION_SHOW_UPLOAD_TAB' => 'Возможность загрузки файла',
			'OPTION_FILE_EXTENSIONS' => 'Расширения файлов (через запятую)',
			'OPTION_ALLOW_ALL_FILES' => 'Разрешить выбор других разрешений',
			'OPTION_SAVE_CONFIG' => 'Сохранять настройки',
		);
		$strResult = $arMess[$Item];
		if (CWD_Pageprops::IsUtf8()) {
			$strResult = $GLOBALS['APPLICATION']->ConvertCharset($strResult, 'CP1251', 'UTF-8');
		}
		return $strResult;
	}
	function ShowSettings($PropertyCode) {
		$arFilter = CWD_Pageprops::GetFilter($PropertyCode, $SiteID);
		$resCurrentProp = CWD_Pageprops::GetList(false,$arFilter);
		if ($arCurrentItem = $resCurrentProp->GetNext()) {
			$arCurrentItem = self::TransformItem($arCurrentItem);
		}
		if (!is_array($arCurrentItem['DATA'])) {
			$arCurrentItem['DATA'] = array();
		}
		$arSites = CWD_Pageprops::GetSites();
		ob_start();
		?>
			<style>
			#wd_pageprops_settings_selectbox .adm-list-table-cell {padding-left:12px; padding-right:12px;}
			#wd_pageprops_settings_selectbox input[type=text] {width:100%; -moz-box-sizing:border-box; -webkit-box-sizing:border-box; box-sizing:border-box;}
			</style>
			<div id="wd_pageprops_settings_filesite">
				<table class="adm-list-table">
					<tbody>
						<tr class="adm-list-table-header">
							<td class="adm-list-table-cell" style="width:40%;">
								<div class="adm-list-table-cell-inner"><?=self::GetMessage('OPTION_COLUMN_PARAM');?></div>
							</td>
							<td class="adm-list-table-cell">
								<div class="adm-list-table-cell-inner"><?=self::GetMessage('OPTION_COLUMN_VALUE');?></div>
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_start_path"><?=self::GetMessage('OPTION_START_PATH');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<input type="text" name="data[start_path]" id="wd_propspage_filesite_start_path" value="<?=$arCurrentItem['DATA']['start_path'];?>" size="50" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_start_site"><?=self::GetMessage('OPTION_START_SITE');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<select name="data[start_site]" id="wd_propspage_filesite_start_site">
									<option value=""><?=self::GetMessage('OPTION_START_SITE_EMPTY');?></option>
									<?foreach($arSites as $arSite):?>
										<option value="<?=$arSite['ID'];?>"<?if($arCurrentItem['DATA']['allow_all_files']==$arSite['ID']):?> selected="selected"<?endif?>>[<?=$arSite['ID'];?>] <?=$arSite['NAME'];?></option>
									<?endforeach?>
								</select>
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_type"><?=self::GetMessage('OPTION_TYPE');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<select name="data[type]" id="wd_propspage_filesite_type">
									<option value="F"<?if($arCurrentItem['DATA']['type']=='F'):?> selected="selected"<?endif?>><?=self::GetMessage('OPTION_TYPE_F');?></option>
									<option value="D"<?if($arCurrentItem['DATA']['type']=='D'):?> selected="selected"<?endif?>><?=self::GetMessage('OPTION_TYPE_D');?></option>
								</select>
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_show_upload_tab"><?=self::GetMessage('OPTION_SHOW_UPLOAD_TAB');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<input type="checkbox" name="data[show_upload_tab]" id="wd_propspage_filesite_show_upload_tab" value="Y"<?if($arCurrentItem['DATA']['show_upload_tab']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_extensions"><?=self::GetMessage('OPTION_FILE_EXTENSIONS');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<input type="text" name="data[extensions]" id="wd_propspage_filesite_extensions" value="<?=$arCurrentItem['DATA']['extensions'];?>" size="50" />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_allow_all_files"><?=self::GetMessage('OPTION_ALLOW_ALL_FILES');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<input type="checkbox" name="data[allow_all_files]" id="wd_propspage_filesite_allow_all_files" value="Y"<?if($arCurrentItem['DATA']['allow_all_files']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
						<tr class="adm-list-table-row">
							<td class="adm-list-table-cell align-right adm-detail-valign-middle">
								<label for="wd_propspage_filesite_save_config"><?=self::GetMessage('OPTION_SAVE_CONFIG');?>:</label>
							</td>
							<td class="adm-list-table-cell align-left">
								<input type="checkbox" name="data[save_config]" id="wd_propspage_filesite_save_config" value="Y"<?if($arCurrentItem['DATA']['save_config']=='Y'):?> checked="checked"<?endif?> />
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<?
		return ob_get_clean();
	}
	function SaveSettings($PropertyCode, $SiteID, $arPost) {
		$arData = $arPost['data'];
		$arData['show_upload_tab'] = $arData['show_upload_tab']=='Y' ? 'Y' : 'N';
		$arData['allow_all_files'] = $arData['allow_all_files']=='Y' ? 'Y' : 'N';
		$arData['save_config'] = $arData['save_config']=='Y' ? 'Y' : 'N';
		$arFields = array(
			'PROPERTY' => $PropertyCode,
			'SITE' => $SiteID,
			'TYPE' => self::GetCode(),
			'DATA' => serialize($arData),
		);
		$arFilter = CWD_Pageprops::GetFilter($PropertyCode, $SiteID);
		$resCurrentProp = CWD_Pageprops::GetList(false,$arFilter);
		if ($arCurrentItem = $resCurrentProp->GetNext(false,false)) {
			if (CWD_Pageprops::Update($arCurrentItem['ID'], $arFields)) {
				return true;
			}
		} else {
			if (CWD_Pageprops::Add($arFields)) {
				return true;
			}
		}
		return false;
	}
	function TransformItem($arItem) {
		$arItem['DATA'] = @unserialize($arItem['~DATA']);
		if (!is_array($arItem['DATA'])) {
			$arItem['DATA'] = array();
		}
		foreach($arItem['DATA'] as $Key => $arOption) {
			if ($Key=='') {
				unset($arItem['DATA'][$Key]);
			}
		}
		return $arItem;
	}
	function ShowControls($arItem, $PropertyCode, $PropertyID, $PropertyValue, $SiteID) {
		$UniqID = rand(100000000,999999999);
		$arItem = self::TransformItem($arItem);
		$arPath = array();
		if (!empty($arItem['DATA']['start_path'])) {
			$arPath['PATH'] = $arItem['DATA']['start_path'];
		}
		if (strlen($arItem['DATA']['start_site'])==2) {
			$arPath['SITE'] = $arItem['DATA']['start_site'];
		}
		$arDialogParams = array(
			'event' => 'WD_Pageprops_SelectFileAction_'.$UniqID,
			'arResultDest' => array('FUNCTION_NAME' => 'WD_Pageprops_OnSelectFile_'.$UniqID),
			'arPath' => $arPath,
			'select' => $arItem['DATA']['type']=='D' ? 'D' : 'F',
			'operation' => 'O',
			'showUploadTab' => $arItem['DATA']['show_upload_tab']=='Y' ? true : false,
			'showAddToMenuTab' => false,
			'fileFilter' => str_replace(' ','',$arItem['DATA']['extensions']),
			'allowAllFiles' => $arItem['DATA']['allow_all_files']=='Y' ? true : false,
			'saveConfig' => $arItem['DATA']['save_config']=='Y' ? true : false,
		);
		ob_start();
		?>
			<?if($GLOBALS['WD_PAGEPROPS_file_dialog.js_INCLUDED']!==true):?>
				<script src="/bitrix/js/main/file_dialog.js"></script>
				<?$GLOBALS['WD_PAGEPROPS_file_dialog.js_INCLUDED']=true;?>
			<?endif?>
			<script>
			function WD_Pageprops_OnSelectFile_<?=$UniqID;?>(FileName,Path,Site){
				if (Path.length>1) {
					Path = Path + '/';
				}
				document.getElementById('WD_Pageprops_FileSite_<?=$UniqID;?>').value = Path + FileName;
			}
			</script>
			<?CAdminFileDialog::ShowScript($arDialogParams);?>
			<input type="text" name="PROPERTY[<?=$PropertyID;?>][VALUE]" id="WD_Pageprops_FileSite_<?=$UniqID;?>" value="<?=$PropertyValue;?>" style="width:80%" />
			<input type="button" value="..." onclick="WD_Pageprops_SelectFileAction_<?=$UniqID;?>()" />
		<?
		$strResult = ob_get_clean();
		return $strResult;
	}
}

?>