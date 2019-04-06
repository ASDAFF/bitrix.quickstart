<?
class CWDA_WebdebugImage extends CWDA_Plugin {
	CONST GROUP = 'IMAGES';
	CONST CODE = 'WEBDEBUG_IMAGE';
	CONST NAME = 'Обработка модулем «Обработчик изображений»';
	CONST DEL_ARRAY = 'WD_IMAGE_DELETE_FILES';
	//
	static function GetDescription() {
		$Descr = 'Плагин для обработки картинок модулем «<a href="http://marketplace.1c-bitrix.ru/solutions/webdebug.image/" tatget="_blank">Обработчик изображений</a>».';
		if (!CWDA::IsUtf()) {
			$Descr = CWDA::ConvertCharset($Descr);
		}
		return $Descr;
	}
	static function GetMessage($Code, $ConvertCharset=false) {
		$MESS = array(
			'PROP_GROUP_1' => 'Откуда',
			'PROP_GROUP_2' => 'Куда',
			'PROP_WD_IMAGE_PROFILE' => 'Профиль обработки изображений',
			//
			'ALERT_NO_SOURCE' => 'Укажите, откуда скопировать изображения',
			'ALERT_NO_TARGET' => 'Укажите, куда скопировать изображения',
			'ALERT_NO_PROFILE_SELECTED' => 'Выберите профиль обработки изображений',
			//
			'SELECT_SOURCE' => 'Выберите поле/свойство с изображением.',
			'SELECT_TARGET' => 'Выберите поле/свойство, куда будет сохраняться изображение.',
			'SELECT_WD_IMAGE_PROFILE' => 'Выберите профиль обработки изображения',
			//
			'SELECT_WD_IMAGE_PROFILE_NOT_SELECTED' => '--- выберите ---',
			'WD_IMAGE_NO_MODULE_INSTALLED' => 'У Вас не установлен <a href="http://marketplace.1c-bitrix.ru/solutions/webdebug.image/" target="_blank">модуль обработки изображений</a>.',
			'WD_IMAGE_PROFILE_NONAME' => '(название не указано)',
		);
		$MESS = trim($MESS[$Code]);
		if ($ConvertCharset && !CWDA::IsUtf()) {
			$MESS = CWDA::ConvertCharset($MESS);
		}
		return $MESS;
	}
	//
	static function AddHeadData() {
		?>
		<script>
		BX.addCustomEvent('onWdaAfterIBlockChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaAfterActionChange', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				WDA_<?=self::CODE?>_Fill();
			}
		});
		BX.addCustomEvent('onWdaBeforeSubmit', function(){
			if(WdaCurrentAction=='<?=self::CODE?>'){
				if ($('#wda_field_source').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_SOURCE',true);?>');
				} else if ($('#wda_field_target').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_TARGET',true);?>');
				} else if ($('#wda_field_wd_image_profile').val()=='') {
					WdaCanSubmit = false;
					alert('<?=self::GetMessage('ALERT_NO_PROFILE_SELECTED',true);?>');
				}
			}
		});
		//
		function WDA_<?=self::CODE?>_Fill(){
			var Select = $('#wda_filter_param');
			// Source
			var SelectSource = $('#wda_field_source').html(Select.html());
			SelectSource.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectSource.find('optgroup option').not('[data-type=F]').remove();
			SelectSource.change();
			// Target
			var SelectTarget = $('#wda_field_target').html(Select.html());
			SelectTarget.find('optgroup').not('optgroup[data-group=FIELDS]').not('optgroup[data-group=PROPERTIES]').remove();
			SelectTarget.find('optgroup option').not('[data-type=F]').remove();
			SelectTarget.change();
		}
		//
		</script>
		<?
	}
	static function ShowSettings($IBlockID=false) {
		?>
		<?if(CModule::IncludeModule('webdebug.image')):?>
			<div id="wda_settings_<?=self::CODE?>">
				<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_1');?></div>
				<div>
					<div><select name="params[field_source]" id="wda_field_source" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_SOURCE'));?></div>
				</div>
				<br/>
				<div class="wda_settings_header"><?=self::GetMessage('PROP_GROUP_2');?></div>
				<div>
					<div><select name="params[field_target]" id="wda_field_target" class="wda_select_field"></select><?=CWDA::ShowHint(self::GetMessage('SELECT_TARGET'));?></div>
				</div>
				<br/>
				<div class="wda_settings_header"><?=self::GetMessage('PROP_WD_IMAGE_PROFILE');?></div>
				<div>
					<div>
						<?self::WdImageGetProfiles()?>
						<select name="params[wd_image_profile]" id="wda_field_wd_image_profile" class="wda_select_field">
							<option value=""><?=self::GetMessage('SELECT_WD_IMAGE_PROFILE_NOT_SELECTED');?></option>
							<?foreach(self::WdImageGetProfiles() as $arProfile):?>
								<option value="<?=$arProfile['ID'];?>">[<?=$arProfile['ID'];?>] <?=$arProfile['NAME'];?></option>
							<?endforeach?>
						</select><?=CWDA::ShowHint(self::GetMessage('SELECT_WD_IMAGE_PROFILE'));?>
					</div>
				</div>
			</div>
		<?else:?>
			<?
				print BeginNote();
				print self::GetMessage('WD_IMAGE_NO_MODULE_INSTALLED');
				print EndNote();
			?>
		<?endif?>
		<?
	}
	static function WdImageGetProfiles(){
		$arResult = array();
		if(CModule::IncludeModule('webdebug.image')){
			$resProfiles = CWebdebugImageProfile::GetList(array('SORT'=>'ASC','NAME'=>'ASC'),array());
			while($arProfile = $resProfiles->GetNext(false,false)){
				if(!CWDA::IsUtf()) {
					$arProfile['NAME'] = CWDA::ConvertCharset($arProfile['NAME'],'CP1251','UTF-8');
				}
				if(empty($arProfile['NAME'])){
					$arProfile['NAME'] = self::GetMessage('WD_IMAGE_PROFILE_NONAME');
				}
				$arResult[] = $arProfile;
			}
		}
		return $arResult;
	}
	static function ProcessImages($ProfileID, $arImageID){
		$arResult = array();
		if(!is_array($arImageID)) {
			$arImageID = array($arImageID);
		}
		foreach($arImageID as $ImageID){
			$strImageSrc = $GLOBALS['APPLICATION']->IncludeComponent(
				"webdebug:image",
				"",
				Array(
					"PROFILE" => $ProfileID,
					"RETURN" => "SRC",
					"CACHE_IMAGE" => "N",
					"IMAGE" => $ImageID,
					"DESCRIPTION" => "",
					"DISPLAY_ERRORS" => "N"
				),
				false,
				array("HIDE_ICONS"=>"Y")
			);
			if(!empty($strImageSrc) && is_file($_SERVER['DOCUMENT_ROOT'].$strImageSrc) && filesize($_SERVER['DOCUMENT_ROOT'].$strImageSrc)>0) {
				$arNewFile = CFile::MakeFileArray($_SERVER['DOCUMENT_ROOT'].$strImageSrc);
				if(is_array($arNewFile)) {
					$arResult[] = $arNewFile;
					$GLOBALS[self::DEL_ARRAY][] = $strImageSrc;
				}
			}
		}
		if(count($arResult)==1) {
			$arResult = $arResult[0];
		}
		return $arResult;
	}
	static function Process($ElementID, $arElement, $Params) {
		$bResult = false;
		// Source
		$SourceField = false;
		if(in_array($Params['field_source'],array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
			$SourceField = $Params['field_source'];
		}
		$SourcePropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_source'],$M)) {
			$SourcePropertyID = IntVal($M[1]);
		}
		// Target
		$TargetField = false;
		if(in_array($Params['field_target'],array('PREVIEW_PICTURE','DETAIL_PICTURE'))) {
			$TargetField = $Params['field_target'];
		}
		$TargetPropertyID = false;
		if(preg_match('#^PROPERTY_(\d+)$#i',$Params['field_target'],$M)) {
			$TargetPropertyID = IntVal($M[1]);
		}
		// Profile
		$ProfileID = IntVal($Params['wd_image_profile']);
		if($ProfileID<=0) {
			return false;
		}
		// Array for delete
		$GLOBALS[self::DEL_ARRAY] = array();
		// Process
		if ((strlen($SourceField) || $SourcePropertyID>0) && (strlen($TargetField) || $TargetPropertyID>0)) {
			$Value = false;
			if (strlen($SourceField)) {
				$Value = $arElement[$SourceField];
			} elseif ($SourcePropertyID>0) {
				$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$SourcePropertyID);
				$Value = $arProp['VALUE'];
			}
			if (!empty($Value)) {
				if (strlen($TargetField)) {
					if (is_array($Value)) {
						$Value = $Value[0];
					}
					if ($Value>0) {
						$Value = self::ProcessImages($ProfileID,$Value);
						$IBlockElement = new CIBlockElement;
						if ($IBlockElement->Update($arElement['ID'],array($TargetField=>$Value))) {
							$bResult = true;
						}
					}
				} elseif ($TargetPropertyID>0) {
					$arProp = CWDA::GetPropertyFromArrayById($arElement['PROPERTIES'],$TargetPropertyID);
					if ($arProp['MULTIPLE']!='Y' && is_array($Value)) {
						$Value = $Value[0];
						$Value = self::ProcessImages($ProfileID,$Value);
					} elseif ($arProp['MULTIPLE']=='Y' && is_array($Value)) {
						foreach($Value as $Key => $FileID){
							$Value[$Key] = self::ProcessImages($ProfileID,$FileID);
						}
					} elseif ($Value>0) {
						$Value = self::ProcessImages($ProfileID,$Value);
					}
					CIBlockElement::SetPropertyValuesEx($arElement['ID'],$arElement['IBLOCK_ID'],array($TargetPropertyID=>$Value));
					$bResult = true;
				}
			}
		}
		foreach($GLOBALS[self::DEL_ARRAY] as $strFile) {
			// Remove file
			@unlink($_SERVER['DOCUMENT_ROOT'].$strFile);
			// Remove dir (recursively from last child to root, but /upload/ and /bitrix/)
			$arFileName = array_slice(explode('/',$strFile),1,-1);
			for($i=count($arFileName); $i>=0; $i--){
				$Dir = implode('/',array_slice($arFileName,0,$i));
				if(!empty($Dir)) {
					if(in_array(ToLower($Dir),array('upload','bitrix'))){
						break;
					}
					$Dir = '/'.$Dir;
					if(is_dir($_SERVER['DOCUMENT_ROOT'].$Dir)){
						@rmdir($_SERVER['DOCUMENT_ROOT'].$Dir);
					}
				}
			}
		}
		return $bResult;
	}
}
?>