<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die();

/**
 * Bitrix vars
 *
 * @var CBitrixComponent         $component
 * @var CBitrixComponentTemplate $this
 * @var array                    $arParams
 * @var array                    $arResult
 * @var array                    $arLangMessages
 * @var array                    $templateData
 *
 * @var string                   $templateFile
 * @var string                   $templateFolder
 * @var string                   $parentTemplateFolder
 * @var string                   $templateName
 * @var string                   $componentPath
 *
 * @var CDatabase                $DB
 * @var CUser                    $USER
 * @var CMain                    $APPLICATION
 */

use \Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$formId = $arParams['UNIQUE_FORM_ID'];
?>
<script type="text/javascript">

	<?=$arResult['GOALS_SETTINGS'];?>

	jQuery(document).ready(function ($) {

		<?=$arResult['JQUERY_AJAX']?>

		<?if($arResult['MESSAGE']):?>
			<?if($arResult['MESSAGE']['HIDDEN']):?>
				$('html, body').animate({
					scrollTop: $('#<?=$arParams['UNIQUE_FORM_ID'];?> .afd-row-danger:first').offset().top - 30
				}, 300, function () {
					$('#<?=$arParams['UNIQUE_FORM_ID'];?> .afd-row-danger:first').addClass('afd-active');
				});
				<?unset($arResult['MESSAGE']['HIDDEN']);?>
			<?else:?>
				$('html, body').animate({
					scrollTop: $('#<?=$arParams['UNIQUE_FORM_ID'];?>').offset().top - 30
				}, 300);
			<?endif?>
		<?endif?>
	});
</script>

<?=bitrix_sessid_post();?>
<input type="text" name="ANTIBOT[NAME]" value="<?=$arResult['ANTIBOT']['NAME'];?>" autocomplete="off" class="afd-antibot">

<?if($arMess = $arResult['MESSAGE']['SUCCESS']):?>

	<div class="afd-alert afd-alert-success">
		<span></span>
		<div class="afd-alert-title"><?=join('<br>', $arMess);?></div>
		<?if($arParams['MESS_SUCCESS_DESC']):?>
			<div class="afd-alert-desc"><?=$arParams['MESS_SUCCESS_DESC']?></div>
		<?endif?>
	</div>

<?else:?>

	<input type="hidden" name="API_FD_AJAX" value="<?=$arParams['UNIQUE_FORM_ID'];?>">
	<input type="hidden" name="UNIQUE_FORM_ID" value="<?=$arParams['UNIQUE_FORM_ID'];?>">

	<? if($arResult['MESSAGE']): ?>
		<? foreach($arResult['MESSAGE'] as $messCode => $arMessVal): ?>
			<div class="afd-alert afd-alert-<?=ToLower($messCode);?>">
				<span></span>
				<div class="afd-alert-title"><?=join('<br>', $arMessVal);?></div>
			</div>
		<? endforeach; ?>
	<? endif; ?>

	<?
	if(!empty($arResult['DISPLAY_PROPERTIES']))
	{
		foreach($arResult['DISPLAY_PROPERTIES'] as $arProp)
		{
			$fieldClass = $fieldData = $form_row_class = $fieldError = $chooseOption = '';

			$fieldId       = ToLower($arParams['UNIQUE_FORM_ID'] . '_field_' . $arProp['CODE']);
			$fieldMulti    = $arProp['MULTIPLE'] == 'Y';
			$fieldMultiCnt = ($fieldMulti && $arProp['MULTIPLE_CNT']) ? $arProp['MULTIPLE_CNT'] : 1;
			$fieldCode     = 'FIELDS['. $arProp['CODE'] .']' . ($fieldMulti ? '[]' : '');
			$fieldReq      = ($arProp['IS_REQUIRED'] == 'Y' ? '<span class="afd-asterisk">*</span>' : '');
			$fieldName     = $arProp['NAME'] . $fieldReq;


			if($arProp['ERROR'] && $arParams['SHOW_ERRORS_BOTTOM'])
				$fieldError = '<div class="afd-error">' . trim($arProp['ERROR']) . '</div>';


			if($arProp['IS_REQUIRED'] == 'Y')
				$form_row_class .= ' afd-row-required';


			if($arProp['IS_REQUIRED'] != 'Y' || ($arProp['LIST_TYPE'] == 'L' && !$fieldMulti))
				$chooseOption = '<option value="">'. $arParams['MESS_CHOOSE'] .'</option>';


			if($arParams['SHOW_ERRORS_IN_FIELD'] && $arProp['ERROR'])
				$form_row_class .= ' afd-row-danger';
			elseif($arProp['USER_VALUE'] && !$arProp['ERROR'])
				$form_row_class .= ' afd-row-success afd-active';


			//if($arProp['USER_TYPE'])
				//$form_row_class .= ' ' . ToLower($arProp['USER_TYPE']);


			//---------- Multiple select size ----------//
			if($fieldMulti || $arProp['ROW_COUNT'] > 1)
				$arProp['ROW_COUNT'] = (count($arProp['DISPLAY_VALUE']) >= 10) ? 10 : intval(count($arProp['DISPLAY_VALUE']));

			if($fieldMulti && $chooseOption)
				$arProp['ROW_COUNT']++;


			//---------- Single select size ----------//
			if(!$fieldMulti && $arProp['ROW_COUNT'] == 1)
				$arProp['ROW_COUNT'] = 1;


			//---------- size attribute ----------//
			$size = ' size="' . $arProp['ROW_COUNT'] . '"';


			//---------- data- attribute ----------//
			if(count($arProp['DATA']))
			{
				foreach($arProp['DATA'] as $data)
					$fieldData .= ' ' . $data;

				unset($data);
			}


			//---------- class attribute ----------//
			$fieldClass .= ToLower('afd-field-'.$arProp['CODE']);


			//---------- User settings in property ----------//
			$arSettings = $arProp['USER_TYPE_SETTINGS'];

			switch($arProp['PROPERTY_TYPE'])
			{
				case 'HIDDEN':
				{
					?>
					<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
					<input type="hidden"
					       name="<?=$fieldCode;?>"
					       id="<?=$fieldId;?>"
					       class="<?=$fieldClass;?>"
					       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
				<? endfor; ?>
					<?
				}
					break;

				case 'DIVIDER':
				{
					?>
					<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
					<div id="<?=$fieldId;?>" class="uk-form-row api-fd-divider"><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></div>
				<? endfor; ?>
					<?
				}
					break;

				case 'N': //number
				case 'S': // textarea + text + DateTime
				{
					?>
					<? if($arProp['USER_TYPE'] == 'APIFD_PSList'): ?>
						<div class="afd-row <?=$form_row_class?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
								<?if($arSettings['VIEW'] == 'R'):?>
									<div class="afd-control afd-field afd-field-multi afd-field-radio">
									<?foreach($arProp['DISPLAY_VALUE'] as $item):?>
										<?
										$isChecked = false;
										if(($item['ID'] == $arProp['USER_VALUE']))
											$isChecked = true;
										?>
										<label for="<?=$fieldId . '_' . $item['ID'];?>" class="api_radio <?=$isChecked ? 'api_active' : '' ?>">
											<input type="radio"
											       name="<?=$fieldCode;?>"
											       value="<?=$item['ID'];?>"
											       class="<?=$fieldClass;?> afd-type-radio"
												    <?=$isChecked ? 'checked=""' : '' ?>
											       id="<?=$fieldId .'_'. $item['ID'];?>" <?=$fieldData;?>> <span class="afd-control-name"><?=$item['NAME'];?></span>
										</label>
									<? endforeach; ?>
									</div>
								<?else:?>
								<div class="afd-control">
									<select name="<?=$fieldCode;?>"
									        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
									        id="<?=$fieldId;?>" <?//=$size?> <?=$fieldData;?>>
										<?=$chooseOption?>
										<?foreach($arProp['DISPLAY_VALUE'] as $item):?>
											<option value="<?=$item['ID'];?>"<? if(($item['ID'] == $arProp['USER_VALUE'])): ?> selected=""<? endif; ?>><?=$item['NAME'];?></option>
										<? endforeach; ?>
									</select>
								</div>
								<?endif?>
								<?=$fieldError?>
							</div>
						</div>
					<? elseif($arProp['USER_TYPE'] == 'HTML' || $arProp['USER_TYPE'] == 'TEXT'): ?>
						<div class="afd-row <?=$form_row_class?> <?=($arParams['WYSIWYG_ON']?'afd-row-wysiwyg':'')?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
								<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
									<div class="afd-control">
										<textarea name="<?=$fieldCode;?>"
										          class="<?=$fieldClass;?> afd-field afd-type-textarea <?=($arProp['USER_TYPE'] == 'HTML' ? 'afd-type-wysiwyg' : '')?>"
										          data-autoresize
										          id="<?=$fieldId;?><?=$fieldMulti ? '_' . $i : '';?>"<?=$fieldData;?>><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></textarea>
									</div>
								<? endfor; ?>
								<?=$fieldError?>
							</div>
						</div>
					<? elseif($arProp['USER_TYPE'] == 'DateTime' || $arProp['USER_TYPE'] == 'Date'): ?>
						<div class="afd-row <?=$form_row_class?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
								<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
									<div class="afd-control">
										<input type="text"
										       name="<?=$fieldCode;?>"
										       id="<?=$fieldId;?><?=$fieldMulti ? '_' . $i : '';?>"
										       class="<?=$fieldClass;?> afd-field afd-type-text afd-type-<?=ToLower($arProp['USER_TYPE'])?>"
										       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
													<i class="api_icon api_icon_calendar"></i>
										<?/* $APPLICATION->IncludeComponent(
											"bitrix:main.calendar",
											"",
											Array(
												"SHOW_INPUT"         => "N",
												"FORM_NAME"          => $formId,
												"INPUT_NAME"         => $fieldMulti ? $fieldId . '_' . $i : $fieldId,
												"INPUT_NAME_FINISH"  => "",
												"INPUT_VALUE"        => "",
												"INPUT_VALUE_FINISH" => "",
												"SHOW_TIME"          => ($arProp['USER_TYPE'] == 'DateTime' ? 'Y' : 'N'),
												"HIDE_TIMEBAR"       => "N",
											),
											null,
											Array('HIDE_ICONS' => 'Y')
										); */?>
									</div>
								<? endfor; ?>
								<?=$fieldError?>
							</div>
						</div>
					<? elseif($arProp['USER_TYPE'] == 'UserID'): ?>
						<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
							<div class="afd-row <?=$form_row_class?>">
								<? if($fieldName): ?>
									<div class="afd-label"><?=$fieldName;?></div>
								<? endif; ?>
								<div class="afd-controls">
									<div class="afd-control <?=($fieldMulti?'afd-control-multiple':'')?>">
										<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>

											<select name="<?=$fieldCode;?>"
											        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
											        id="<?=$fieldId;?>" <?=$size?> <?=$fieldData;?>>
												<?=$chooseOption?>
												<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
													<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
												<? endforeach; ?>
											</select>
										<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>

											<select name="<?=$fieldCode;?>"
											        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
											        id="<?=$fieldId;?>"
											        multiple="" <?=$size?> <?=$fieldData;?>>
													<?=$chooseOption?>
													<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
														<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
													<? endforeach; ?>
											</select>
										<? endif; ?>
									</div>
									<?=$fieldError?>
								</div>
							</div>
						<? endif; ?>
					<? elseif($arProp['USER_TYPE'] == 'directory'): ?>
						<div class="afd-row afd-row-static <?=$form_row_class?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
								<? if($arProp['DISPLAY_VALUE']): ?>
									<div class="afd-hl-list afd-hl-list-<?=ToLower($arProp['DISPLAY_TYPE'])?>  afd-hl-<?=$fieldMulti?'checkbox':'radio'?>">
										<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
											<?
											$itemActive = false;
											if($fieldMulti){
												if(($v['UF_DEF'] == 1 && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($v['UF_XML_ID'], $arProp['USER_VALUE'])))
													$itemActive = true;
											}
											else{
												if(($v['UF_DEF'] == 1 && !$arResult['POST']) || ($arProp['USER_VALUE'] && $v['UF_XML_ID'] == $arProp['USER_VALUE']))
													$itemActive = true;
											}
											?>
											 <div class="afd-hl-item<?=$itemActive?' afd-hl-active':''?>">
												 <label>
													 <?if($fieldMulti):?>
														 <input type="checkbox" name="<?=$fieldCode;?>" value="<?=$v['UF_XML_ID']?>"<?=$itemActive?' checked=""':''?>>
													 <?else:?>
														 <input type="radio" name="<?=$fieldCode;?>" value="<?=$v['UF_XML_ID']?>"<?=$itemActive?' checked=""':''?>>
													 <?endif?>
													 <?if($v['UF_FILE']):?>
													  <span class="afd-hl-icon"><i style="background-image: url('<?=$v['UF_FILE']['SRC']?>');"></i></span>
													 <?endif?>
													 <span class="afd-hl-name"><?=$v['UF_NAME']?></span>
												 </label>
											 </div>
										<? endforeach; ?>
									</div>
								<? endif; ?>
								<?=$fieldError?>
							</div>
						</div>
					<? elseif($arProp['USER_TYPE'] && $arProp['GetPublicEditHTML']): ?>
						<div class="afd-row <?=$form_row_class?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
							 <?
							 //echo "<pre>"; print_r($arProp);echo "</pre>";
							 //CIBlockPropertyMapYandex::GetPublicEditHTML();
							 //CIBlockPropertyDate::GetPublicEditHTML();
							 //Ycaweb\CIBlockPropertyMap2GIS::GetPublicEditHTML();
							 echo call_user_func_array($arProp['GetPublicEditHTML'],
								array(
									 $arProp,
									 array(
											'VALUE'       => $arProp['USER_VALUE'],
											'DESCRIPTION' => "",
									 ),
									 array(
											'VALUE'       => $fieldCode,
											'DESCRIPTION' => '',
											'FORM_NAME'   => $formId,
									 ),
							 ));
							?>
							</div>
						</div>
					<? else: ?>
						<div class="afd-row <?=$form_row_class?>">
							<? if($fieldName): ?>
								<div class="afd-label"><?=$fieldName;?></div>
							<? endif; ?>
							<div class="afd-controls">
								<? for($i = 0; $i < $fieldMultiCnt; $i++): ?>
									<div class="afd-control">
										<? if($arProp['ROW_COUNT'] > 1): ?>
											<textarea name="<?=$fieldCode;?>"
											          id="<?=$fieldId;?>"
											          class="<?=$fieldClass;?> afd-field afd-type-textarea"
											          data-autoresize
											          cols="<?=$arProp['COL_COUNT']?>"
											          rows="<?=$arProp['ROW_COUNT']?>"><?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?></textarea>
										<? else: ?>
											<input type="text"
											       name="<?=$fieldCode;?>"
											       id="<?=$fieldId;?>"
											       class="<?=$fieldClass;?> afd-field afd-type-text"
											       value="<?=$fieldMulti ? $arProp['USER_VALUE'][ $i ] : $arProp['USER_VALUE'];?>"<?=$fieldData;?>>
										<? endif; ?>
									</div>
								<? endfor; ?>
								<?=$fieldError?>
							</div>
						</div>
					<? endif; ?>
					<?
				}
				break;

				// select + checkbox + radio
				case 'L':
				{
					?>
					<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
					<div class="afd-row <?=$form_row_class?>">
						<? if($fieldName): ?>
							<div class="afd-label"><?=$fieldName;?></div>
						<? endif; ?>
						<div class="afd-controls">
							<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>
								<div class="afd-control">
									<select name="<?=$fieldCode;?>"
									        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
									        id="<?=$fieldId;?>" <?=$size?> <?=$fieldData;?>>
										<?=$chooseOption?>
										<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
											<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || $arProp['USER_VALUE'] == $k): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
										<? endforeach; ?>
									</select>
								</div>
							<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>
								<div class="afd-control <?=($fieldMulti?'afd-control-multiple':'')?>">
									<select name="<?=$fieldCode;?>"
									        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
									        id="<?=$fieldId;?>"
									        multiple="" <?=$size?> <?=$fieldData;?>>
										<?=$chooseOption?>
										<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
											<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
										<? endforeach; ?>
									</select>
								</div>
							<? elseif($arProp['LIST_TYPE'] == 'C' && $fieldMulti): ?>
								<div class="afd-control afd-field afd-field-multi afd-field-checkbox">
									<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
										<?
										$isChecked = false;
										if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE'])))
											$isChecked = true;
										?>
										<label for="<?=$fieldId;?>_<?=$k;?>" class="api_checkbox <?=$isChecked ? 'api_active' : '' ?>">
											<input type="checkbox"
											       name="<?=$fieldCode;?>"
											       id="<?=$fieldId;?>_<?=$k;?>"
											       class="afd-type-checkbox"
												    <?=$isChecked ? 'checked=""' : '' ?>
														<? if($v === reset($arProp['DISPLAY_VALUE'])): ?>
															data-validation-group="<?=$fieldId;?>"
															<?=$fieldData;?>
														<? endif; ?>
												    value="<?=$k;?>"> <?=$v['VALUE'];?></label>
									<? endforeach; ?>
								</div>
							<? else: ?>
								<div class="afd-control afd-field afd-field-multi afd-field-radio">
									<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
										<?
										$isChecked = false;
										if(($v['DEF'] == 'Y' && !$arResult['POST']) || $arProp['USER_VALUE'] == $k)
											$isChecked = true;
										?>
										<label for="<?=$fieldId;?>_<?=$k;?>" class="api_radio <?=$isChecked ? 'api_active' : '' ?>">
											<input type="radio"
											       name="<?=$fieldCode;?>"
											       id="<?=$fieldId;?>_<?=$k;?>"
											       class="afd-type-radio"
												      <?=$isChecked ? 'checked=""' : '' ?>
															<? if($v === reset($arProp['DISPLAY_VALUE']) && !$arParams['MESS_CHOOSE']): ?>
																data-validation-group="<?=$fieldId;?>"
																<?=$fieldData;?>
															<? endif; ?>
												    value="<?=$k;?>"> <?=$v['VALUE'];?></label>
									<? endforeach; ?>
								</div>

							<? endif; ?>
							<?=$fieldError?>
						</div>
					</div>
				<? endif; ?>
					<?
				}
					break;

				case 'E': // Link to elements
				case 'G': // Link to section
				{
					?>
					<? if(!empty($arProp['DISPLAY_VALUE'])): ?>
					<div class="afd-row <?=$form_row_class?>">
						<? if($fieldName): ?>
							<div class="afd-label"><?=$fieldName;?></div>
						<? endif; ?>
						<div class="afd-controls">
							<div class="afd-control <?=($fieldMulti?'afd-control-multiple':'')?>">
								<?if($arProp['USER_TYPE'] == 'APIFD_ESList'):?>
									<?
									$bShowPicture = $arSettings['SHOW_PICTURE'];
									?>
									<select name="<?=$fieldCode;?>"
									        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single <?if($bShowPicture):?>afd-show-picture<?endif?>"
									        id="<?=$fieldId;?>" <?//=$size?> <?=$fieldData;?>>
										<?=$chooseOption?>
										<? foreach($arProp['DISPLAY_VALUE'] as $k => $section): ?>
										<optgroup label="<?=$section['NAME']?>">
											<?foreach($section['ITEMS'] as $item):?>
												<option value="<?=$item['ID'];?>"
											        <? if($item['PICTURE']): ?>
												        data-picture="<?=$item['PICTURE']['SRC']?>"
												        data-width="<?=$item['PICTURE']['WIDTH']?>"
												        data-height="<?=$item['PICTURE']['HEIGHT']?>"
											        <? endif; ?>
													  <? if(($item['ID'] == $arProp['USER_VALUE'])): ?>selected=""<? endif; ?>><?=$item['NAME'];?></option>
											<? endforeach; ?>
										</optgroup>
										<? endforeach; ?>
									</select>

								<?else:?>

									<? if($arProp['LIST_TYPE'] == 'L' && !$fieldMulti): ?>
										<select name="<?=$fieldCode;?>"
										        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-single"
										        id="<?=$fieldId;?>" <? //=$size?> <?=$fieldData;?>>
											<?=$chooseOption?>
											<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
												<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || ($k == $arProp['USER_VALUE'])): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
											<? endforeach; ?>
										</select>
									<? elseif($arProp['LIST_TYPE'] == 'L' && $fieldMulti): ?>
										<select name="<?=$fieldCode;?>"
										        class="<?=$fieldClass;?> afd-field afd-type-select afd-select-multiple"
										        id="<?=$fieldId;?>"
										        multiple="" <?=$size?> <?=$fieldData;?>>
											<?=$chooseOption?>
											<? foreach($arProp['DISPLAY_VALUE'] as $k => $v): ?>
												<option value="<?=$k;?>"<? if(($v['DEF'] == 'Y' && !$arResult['POST']) || (is_array($arProp['USER_VALUE']) && in_array($k, $arProp['USER_VALUE']))): ?> selected=""<? endif; ?>><?=$v['VALUE'];?></option>
											<? endforeach; ?>
										</select>
									<? endif; ?>
								<?endif?>
							</div>
							<?=$fieldError?>
						</div>
					</div>
				<? endif; ?>
					<?
				}
					break;


				case 'F':
				{
					?>
					 <div class="afd-row afd-row-type-upload <?=$form_row_class?>">
						 <? if($fieldName): ?>
							 <div class="afd-label"><?=$fieldName;?></div>
						 <? endif; ?>
						 <div class="afd-controls">
							 <?=$fieldError?>
							 <div class="api_upload" id="<?=$fieldId;?>">
								 <ul class="api_file_list">
									<? if($arProp['DISPLAY_VALUE']): ?>
										<? foreach($arProp['DISPLAY_VALUE'] as $file): ?>
											<li>
												<div class="api_progress_bar">
													<div class="api_progress" rel="100" style="width: 100%;"></div>
													<div class="api_file_remove" data-code="<?=$file['code']?>" data-type="<?=$file['type']?>"></div>
												</div>
												<div class="api_file_label">
													<span class="api_file_ext_<?=GetFileExtension($file['name'])?>"></span>
													<span class="api_file_name"><?=$file['name']?></span>
													<span class="api_file_size"><?=$file['size_round']?></span>
												</div>
											</li>
										<? endforeach; ?>
									<? endif; ?>
								 </ul>
								 <div class="api_upload_drop">
									 <span class="api_upload_drop_icon"></span>
									 <span class="api_upload_drop_text"><?=Loc::getMessage('AFD_AJAX_UPLOAD_DROP')?></span>
									 <input id="<?=$fieldId;?>_file"
									        class="api_upload_file <?=$fieldClass;?>"
									        type="file"
									        name="<?=$fieldCode;?>"
										  <?=($fieldMulti?'multiple=""':'')?>
										  <?=$fieldData;?>>
								 </div>
								 <div class="api_upload_info">
									 <?=Loc::getMessage('AFD_AJAX_UPLOAD_INFO', array(
										  '#UPLOAD_FILE_SIZE#'  => $arParams['UPLOAD_FILE_SIZE'],
										  '#UPLOAD_FILE_LIMIT#' => $arParams['UPLOAD_FILE_LIMIT'],
										  '#FILE_TYPE#'         => $arProp['FILE_TYPE'],
									 ))?>
								 </div>
							 </div>
						 </div>
						 <script type="text/javascript">
							 jQuery(document).ready(function ($) {
								 $('#<?=$fieldId;?>').apiUpload({
									 fileName: '<?=$arProp['CODE'];?>',
									 maxFiles: <?=$arParams['UPLOAD_FILE_LIMIT'];?>,
									 maxFileSize: <?=$arParams['UPLOAD_MAX_FILESIZE'];?>,
									 extFilter: '<?=$arProp['FILE_TYPE']?>',
									 extraData: {
										 'sessid': BX.bitrix_sessid(),
										 'API_FD_ACTION': 'FILE_UPLOAD',
										 'UNIQUE_FORM_ID': '<?=$arParams['UNIQUE_FORM_ID'];?>',
									 },
									 errors: {
										 onFileSizeError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileSizeError')?>',
										 onFileTypeError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileTypeError')?>',
										 onFileExtError:  '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFileExtError')?>',
										 onFilesMaxError: '<?=Loc::getMessage('AFD_AJAX_UPLOAD_onFilesMaxError')?>',
									 },
									 callback: {
										 onError: function (node, errors) {
											 var mess = '';
											 for (var i in errors) {
												 mess += errors[i] + "<br>";
											 }
											 $.fn.apiAlert({
												 content: mess,
											 });
										 },
										 onFallbackMode: function(message) {
										 	$('#<?=$fieldId;?> .api_upload_drop').html(message);
											 console.error(message);
										 },
									 }
								 });

								 $('#<?=$fieldId;?>').on('click','.api_file_remove',function(){
								 	var fileButton = $(this);
								 	var fileCode = $(this).data('code') || '';
								 	if(fileCode.length){
									  $.ajax({
										  type: 'POST',
										  cache: false,
										  data: {
											  'sessid': BX.bitrix_sessid(),
											  'API_FD_ACTION': 'FILE_DELETE',
											  'UNIQUE_FORM_ID': '<?=$arParams['UNIQUE_FORM_ID'];?>',
											  'FILE_NAME': '<?=$arProp['CODE'];?>',
											  'FILE_CODE': fileCode,
										  },
										  success: function () {
											  $(fileButton).closest('li').remove();
										  }
									  });
								  }
								  else{
									  $(fileButton).closest('li').remove();
								  }
								 })
							 })
						 </script>
					 </div>
					<?
				}
			}
		}
	}
	?>

	<?if($arParams['USE_BX_CAPTCHA']):?>
		<div id="<?=$formId?>_afd_row_captcha_sid" class="afd-row afd-row-static afd-row-captcha_sid">
			<div class="afd-label"><?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_SID')?></div>
			<div class="afd-controls">
				<input type="hidden" name="CAPTCHA[SID]" value="<?=$arResult['CAPTCHA_CODE']?>">
				<img src="/bitrix/tools/captcha.php?captcha_sid=<?=$arResult['CAPTCHA_CODE']?>"
				     width="180" height="40" alt="<?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_LOADING')?>">
				<span class="afd-captcha-refresh afd-icon-refresh" title="<?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_REFRESH')?>"></span>
			</div>
		</div>
		<div id="<?=$formId?>_afd_row_captcha_word" class="afd-row afd-row-required afd-row-captcha_word <?=($arResult['CAPTCHA_MESSAGE'] ? 'afd-row-danger afd-active' : '')?>">
			<div class="afd-label"><?=Loc::getMessage('AFD_AJAX_FIELD_CAPTCHA_WORD')?><span class="afd-asterisk">*</span></div>
			<div class="afd-controls">
				<div class="afd-control">
					<input type="text" name="CAPTCHA[WORD]" maxlength="50" value="" autocomplete="off"
					       class="afd-field afd-field-captcha-word afd-type-text">
				</div>
				<?if($arResult['CAPTCHA_MESSAGE']):?>
					<div class="afd-error"><?=$arResult['CAPTCHA_MESSAGE']?></div>
				<?endif?>
			</div>
		</div>
	<?endif?>

	<? if($arParams['USE_EULA'] && $arParams['MESS_EULA']): ?>
		<div class="afd-row afd-row-eula afd-row-accept">
			<div class="afd-controls">
				<label class="afd-accept-label">
					<input type="checkbox" name="EULA_ACCEPTED" value="Y" class="api-field" <?=$arResult['EULA_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
					<div class="afd-accept-text"><?=$arParams['MESS_EULA']?></div>
				</label>
			</div>
		</div>
	<? endif ?>

	<? if($arParams['USE_PRIVACY'] && $arParams['MESS_PRIVACY']): ?>
		<div class="afd-row afd-row-privacy afd-row-accept">
			<div class="afd-controls">
				<label class="afd-accept-label">
					<input type="checkbox" name="PRIVACY_ACCEPTED" value="Y" class="api-field" <?=$arResult['PRIVACY_ACCEPTED'] == 'Y' ? ' checked' : ''?>>
					<div class="afd-accept-text">
						<? if($arParams['MESS_PRIVACY_LINK']): ?>
							<a rel="nofollow" href="<?=$arParams['MESS_PRIVACY_LINK']?>" target="_blank"><?=$arParams['MESS_PRIVACY']?></a>
						<? else: ?>
							<?=$arParams['MESS_PRIVACY']?>
						<? endif ?>
					</div>
				</label>
			</div>
		</div>
	<? endif ?>

	<?if($arParams['USER_CONSENT']):?>
		<div class="afd-row afd-row-user-consent afd-row-accept">
			<div class="afd-controls">
				<?foreach($arResult['DISPLAY_USER_CONSENT'] as $agreementId=>$arAgreement):?>
					<div class="afd-control <?=$arAgreement['ERROR']?'afd-row-danger':''?>">
						<div class="afd-accept-label" data-id="<?=$agreementId?>">
							<input type="checkbox"
							       name="USER_CONSENT[]"
							       value="<?=$agreementId?>"
								 <?=($arParams['USER_CONSENT_IS_CHECKED'] == 'Y' || $arAgreement['USER_VALUE'] == $agreementId) ? 'checked=""' : ''?>>
							<div class="afd-accept-text"><?=$arAgreement['LABEL_TEXT'];?></div>
							<?if($arAgreement['ERROR']):?>
								<div class="afd-error"><?=$arAgreement['ERROR']?></div>
							<?endif?>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
		<?/*$APPLICATION->IncludeComponent(
			 "bitrix:main.userconsent.request",
			 "",
			 array(
					"ID" => $arParams["USER_CONSENT_ID"],
					"IS_CHECKED" => $arParams["USER_CONSENT_IS_CHECKED"],
					"AUTO_SAVE" => "Y",
					"IS_LOADED" => $arParams["USER_CONSENT_IS_LOADED"],
					"REPLACE" => array(
						 'button_caption' => 'Subscribe!',
						 'fields' => array('FIELDS[EMAIL]')
					),
			 )
		);*/?>
	<?endif;?>

	<div class="afd-row">
		<div class="afd-controls">
			<button type="submit"
			        name="API_FD_SUBMIT"
			        value="Y"
			        class="<?=$arParams['SUBMIT_BUTTON_CLASS'];?><?=$BUTTON_SIZE?>">
				<span><?=$arParams['SUBMIT_BUTTON_TEXT'];?></span></button>
		</div>
	</div>
<?endif;?>