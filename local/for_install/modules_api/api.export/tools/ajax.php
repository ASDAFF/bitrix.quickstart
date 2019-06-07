<?

use \Bitrix\Main\Loader;
use \Bitrix\Main\Application;
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Web\Json;

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
Loc::loadMessages(__FILE__);


if(!check_bitrix_sessid())
	die('sess expired');


global $DB, $APPLICATION;
CUtil::JSPostUnescape();

$POST     = $_POST['PROFILE'];
$response = array();

if(!Loader::includeModule('api.export')) {
	$response = array(
		 'result'  => 'error',
		 'message' => Loc::getMessage('AETA_MODULE_ERROR'),
	);
}

$rights = $APPLICATION->GetGroupRight('api.export');
if($rights < 'W') {
	die('Access denied!');
}

use \Api\Export\ProfileTable;
use \Api\Export\Tools;


$exec_action();


///////////////////////////////////////////////////////////////////////////////
// functions
///////////////////////////////////////////////////////////////////////////////

//IBLOCK_ID
function changeIblockTypeId()
{
	global $response, $POST;

	$arCatalogs = Tools::getCatalogs($POST['USE_CATALOG'] == 'Y');

	ob_start();
	?>
	<select name="PROFILE[IBLOCK_ID]" size="5">
		<? foreach($arCatalogs as $key => $arCatalog): ?>
			<?
			if($arCatalog['ID'] != $POST['IBLOCK_TYPE_ID'])
				continue;
			?>
			<? foreach($arCatalog['IBLOCK'] as $id => $iblock): ?>
				<? $selected = ($id == $POST['IBLOCK_ID'] ? ' selected' : ''); ?>
				<option value="<?=$id?>"<?=$selected?>><?=$iblock?></option>
			<? endforeach ?>
		<? endforeach ?>
	</select>
	<?
	$html = ob_get_clean();
	ob_end_clean();

	$response = array(
		 'result'  => 'ok',
		 'message' => '',
		 'items'   => array(
				array(
					 'id'   => '#api_iblock_id',
					 'html' => $html,
				),
		 ),
	);
}

//SECTION_ID
function changeIblockId()
{
	global $response, $POST;


	$arCatalogSections = Tools::getCatalogSections($POST['IBLOCK_ID'], $POST['USE_SUBSECTIONS'] == 'Y');
	$cnt               = count($arCatalogSections);
	$attr_size         = ($cnt > 10 ? 10 : $cnt);
	ob_start();
	?>
	<select name="PROFILE[SECTION_ID][]" size="<?=$attr_size?>" multiple>
		<option value=""<? if(!$POST['SECTION_ID'][0]): ?> selected<? endif ?>><?=Loc::getMessage("AYI_SELECT_OPTION_ALL")?></option>
		<? foreach($arCatalogSections as $id => $section): ?>
			<? $selected = (isset($POST['SECTION_ID']) && in_array($id, $POST['SECTION_ID'])) ? 'selected="selected"' : ''; ?>
			<option value="<?=$id?>" <?=$selected?>><?=$section?></option>
		<? endforeach; ?>
	</select>
	<?
	$html = ob_get_clean();
	ob_end_clean();

	$response = array(
		 'result'  => 'ok',
		 'message' => '',
		 'items'   => array(
				array(
					 'id'   => '#api_iblock_section_id',
					 'html' => $html,
				),
		 ),
	);
}

//IBLOCK_TYPE_ID + IBLOCK_ID + SECTION_ID
function changeUseCatalog()
{
	global $response, $POST;

	$arCatalogs = Tools::getCatalogs($POST['USE_CATALOG'] == 'Y');

	$arCatalogSections = Tools::getCatalogSections($POST['IBLOCK_ID'], $POST['USE_SUBSECTIONS'] == 'Y');
	ob_start();
	?>
	<select name="PROFILE[IBLOCK_TYPE_ID]" size="5">
		<? if($arCatalogs): ?>
			<? foreach($arCatalogs as $arCatalog): ?>
				<option value="<?=$arCatalog['ID']?>"<?=((!isset($POST['IBLOCK_TYPE_ID']) && $arCatalog['DEF'] == 'Y') || ($arCatalog['ID'] == $POST['IBLOCK_TYPE_ID'])) ? " selected" : ""?>><?=$arCatalog['NAME']?></option>
			<? endforeach; ?>
		<? endif ?>
	</select>
	<?
	$data1 = ob_get_clean();
	ob_end_clean();

	ob_start();
	?>
	<select name="PROFILE[IBLOCK_ID]" size="5">
		<? if($POST['IBLOCK_ID'] && $arCatalogs): ?>
			<? foreach($arCatalogs as $arCatalog): ?>
				<? if($arCatalog['IBLOCK']): ?>
					<? foreach($arCatalog['IBLOCK'] as $id => $iblock): ?>
						<? $selected = ($id == $POST['IBLOCK_ID'] ? ' selected' : ''); ?>
						<option value="<?=$id?>"<?=$selected?>><?=$iblock?></option>
					<? endforeach ?>
				<? endif ?>
			<? endforeach ?>
		<? endif ?>
	</select>
	<?
	$data2 = ob_get_clean();
	ob_end_clean();


	$cnt       = count($arCatalogSections);
	$attr_size = ($cnt > 10 ? 10 : $cnt);
	ob_start();
	?>
	<select name="PROFILE[SECTION_ID][]" size="<?=$attr_size?>" multiple>
		<option value=""><?=Loc::getMessage("AYI_SELECT_OPTION_EMPTY")?></option>
		<? if($POST['SECTION_ID'] && $arCatalogSections): ?>
			<? foreach($arCatalogSections as $id => $section): ?>
				<? $selected = ($id == $POST['SECTION_ID'] ? ' selected' : ''); ?>
				<option value="<?=$id?>"<?=$selected?>><?=$section?></option>
			<? endforeach; ?>
		<? endif ?>
	</select>
	<?
	$data3 = ob_get_clean();
	ob_end_clean();

	$response = array(
		 'result'  => 'ok',
		 'message' => '',
		 'items'   => array(
				array(
					 'id'   => '#api_iblock_type_id',
					 'html' => $data1,
				),
				array(
					 'id'   => '#api_iblock_id',
					 'html' => $data2,
				),
				array(
					 'id'   => '#api_iblock_section_id',
					 'html' => $data3,
				),
		 ),
	);
}

//SECTION_ID
function changeUseSubsections()
{
	changeIblockId();
}

//FIELDS
function changeOfferType()
{
	global $response, $POST, $APPLICATION, $isCustom, $customId;

	$isCatalog = Loader::includeModule('catalog');

	$arFields = array();

	$arOfferType = Tools::getOfferType($POST['TYPE']);

	$typeFields = $arOfferType['FIELDS'];

	// огда запрашиваетс€ а€ксом кастомное поле-параметр
	if($isCustom) {
		$arFields = array($typeFields['param']);
	}
	else {

		$profileFields = array();
		if($POST['ID']) {

			$profile = ProfileTable::getRowById($POST['ID']);
			ProfileTable::decodeFields($profile);

			if($profile['FIELDS'] && $profile['TYPE'] == $POST['TYPE']) {
				if($profileFields = $profile['FIELDS']) {

					//ѕроверим наличие тегов профил€ в тегах модул€, все неизвестные теги уд€л€тс€ после сохранени€
					$profileKeys = array();
					foreach($profileFields as $key => $val) {
						if(array_key_exists($val['CODE'], $typeFields))
							$profileKeys[] = $val['CODE'];
						else
							unset($profileFields[ $key ]);
					}
					unset($key, $val);

					foreach($typeFields as $key => $val) {
						if(!in_array($key, $profileKeys))
							$profileFields[] = $val;
					}
				}
			}
		}

		$arFields = ($profileFields ? $profileFields : $typeFields);
	}

	$i = ($customId ? $customId : 0);

	$useOffers = false;
	if($iblockId = $POST['IBLOCK_ID']) {
		Tools::construct();
		Tools::getIblockInfo($iblockId);
		$useOffers = Tools::$useOffers;
	}

	ob_start();
	?>
	<? foreach($arFields as $arField): ?>
	<?
	$id    = $arField['CODE'];
	$rowId = 'row_' . $i;

	//ƒобавим отсутствующие параметры пол€ из описаний типов модул€
	if($tmpField = $typeFields[ $id ]) {
		foreach($tmpField as $key => $val) {
			if(!isset($arField[ $key ]))
				$arField[ $key ] = $val;
		}
	}

	if(empty($arField['TYPE']))
		$arField['TYPE'] = array('NONE');

	$useCondition  = $arField['USE_CONDITIONS'] == 'Y' ? 'checked' : '';
	$hideCondition = $useCondition ? '' : 'hide';

	$useLogic  = $arField['USE_LOGIC'] == 'Y' ? 'checked' : '';
	$hideLogic = $useLogic ? '' : 'hide';

	$required = $arField['REQUIRED'] == 'Y' ? 'checked' : '';

	//TODO: ќб€зательное поле в определенных услови€х сделать типа warning
	$labelClass = '';
	if($required) {
		$labelClass = 'api-label-danger';
	}
	elseif($isCustom || $arField['IS_CUSTOM']) {
		$labelClass = 'api-label-secondary';
	}
	?>
	<tr class="offer-type-field" data-id="<?=$id?>" id="<?=$rowId?>">
		<td class="td-text">
			<div>
				<p>
					<span class="api-label <?=$labelClass?>"><?=$id?></span>
					<?=($isCustom || $arField['IS_CUSTOM'] ? '<button type="button" class="adm-btn adm-btn-icon adm-btn-delete" onclick="customFieldRemove(this);" style="margin-left: 5px"></button>' : '')?>
				</p>
				<? /* if($required): ?>
					<i class="api-icon-warning" title="<?=Loc::getMessage('AYI_XML_OFFER_LABEL_REQUIRED_HINT')?>"></i>
				<? endif */ ?>
				<?=$arField['NAME']?>
			</div>
		</td>
		<td class="td-condition">
			<? if($isCustom || $arField['IS_CUSTOM']): ?>
				<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][IS_CUSTOM]" value="1">

				<?if($id == 'enclosure'):?>
					<input type="text" name="PROFILE[FIELDS][<?=$i?>][CODE]" value="<?=$id?>" placeholder="enclosure">
				<?else:?>
					<input type="text" name="PROFILE[FIELDS][<?=$i?>][CODE]" value="<?=$id?>" placeholder="param">
					<input type="text" name="PROFILE[FIELDS][<?=$i?>][UNIT_VALUE]" value="<?=$arField['UNIT_VALUE']?>" placeholder="unit">
				<? endif ?>
			<? else: ?>
				<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][CODE]" value="<?=$id?>" readonly="">
				<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][IS_CUSTOM]" value="0">
			<? endif ?>

			<? foreach($arField['TYPE'] as $typeKey => $typeId): ?>
				<?
				$typeValue = $arField['VALUE'][ $typeKey ];
				?>
				<div class="field-row">
					<div class="type_row">
						<select name="PROFILE[FIELDS][<?=$i?>][TYPE][]" onchange="getOfferFieldsSelect(this,'<?=$rowId?>')" class="btn-block">
							<?=Tools::showFieldTypeSelect($POST['IBLOCK_ID'], $typeId, $typeValue);?>
						</select>
					</div>
					<div class="value_row <?=($typeId == 'NONE' ? 'hide' : '')?>">
						<select name="PROFILE[FIELDS][<?=$i?>][VALUE][]" class="btn-block">
							<?
							if($typeId != 'NONE')
								echo Tools::showOfferFieldsSelect($POST['IBLOCK_ID'], $typeId, $typeValue);
							?>
						</select>
					</div>
					<div class="controls">
						<button type="button" class="adm-btn adm-btn-icon adm-btn-add"></button>
						<button type="button" class="adm-btn adm-btn-icon adm-btn-delete" <?=$typeKey == 0 ? 'disabled' : ''?>></button>
					</div>
				</div>
			<? endforeach; ?>

		</td>
		<td class="td-option">
			<div class="options_block">
				<label>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][REQUIRED]" value="N">
					<input id="<?=$rowId?>_required_checkbox" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][REQUIRED]" value="Y" <?=$required?>>
					<?=Loc::getMessage('AETA_REQUIRED_LABEL')?>
				</label>

				<label>
					<?
					$useConcat = $arField['USE_CONCAT'] == 'Y';
					$concatValues = (array)Loc::getMessage('AETA_USE_CONCAT_VALUES');
					?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_CONCAT]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_CONCAT]" value="Y" <?=$useConcat ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_USE_CONCAT_LABEL')?> <br>
					<div class="option_value <?=($useConcat ? '' : 'hide')?>">
						<select name="PROFILE[FIELDS][<?=$i?>][CONCAT_VALUE]" class="btn-block">
							<?
							if($concatValues) {
								foreach($concatValues as $key => $value) {
									$selected = ($arField['CONCAT_VALUE'] == $key) ? ' selected' : '';
									?>
									<option value="<?=$key?>"<?=$selected?>><?=$value?></option><?
								}
								unset($key, $value, $selected, $useConcat);
							}
							?>
						</select>
					</div>
				</label>

				<label>
					<? $useFunction = $arField['USE_FUNCTION'] == 'Y'; ?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_FUNCTION]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_FUNCTION]" value="Y" <?=$useFunction ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_FUNCTION_LABEL')?> <br>
					<div class="option_value <?=($useFunction ? '' : 'hide')?>">
						<input type="text" name="PROFILE[FIELDS][<?=$i?>][FUNCTION]" value="<?=$arField['FUNCTION']?>" size="40" placeholder="fn_htmlspecialchars" class="btn-block">
					</div>
				</label>

				<label>
					<? $useTextLength = $arField['USE_TEXT_LENGTH'] == 'Y'; ?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_TEXT_LENGTH]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_TEXT_LENGTH]" value="Y" <?=$useTextLength ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_TEXT_LENGTH_LABEL')?> <br>
					<div class="option_value <?=($useTextLength ? '' : 'hide')?>">
						<input type="text" name="PROFILE[FIELDS][<?=$i?>][TEXT_LENGTH]" value="<?=$arField['TEXT_LENGTH']?>" size="40" placeholder="3000" class="btn-block">
					</div>
				</label>

				<label>
					<? $useDateFormat = $arField['USE_DATE_FORMAT'] == 'Y'; ?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_DATE_FORMAT]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_DATE_FORMAT]" value="Y" <?=$useDateFormat ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_DATE_FORMAT_LABEL')?> <br>
					<div class="option_value <?=($useDateFormat ? '' : 'hide')?>">
						<input type="text" name="PROFILE[FIELDS][<?=$i?>][DATE_FORMAT_VALUE]" value="<?=$arField['DATE_FORMAT_VALUE']?>" size="40" placeholder="d-m-Y H:i:s" class="btn-block">
					</div>
				</label>

				<label>
					<? $useText = $arField['USE_TEXT'] == 'Y'; ?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_TEXT]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_TEXT]" value="Y" <?=$useText ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_TEXT_VALUE_LABEL')?> <br>
					<div class="option_value <?=($useText ? '' : 'hide')?>">
						<textarea name="PROFILE[FIELDS][<?=$i?>][TEXT_VALUE]" class="btn-block"><?=$arField['TEXT_VALUE']?></textarea>
					</div>
				</label>

				<label>
					<?
					$useBoolean = $arField['USE_BOOLEAN'] == 'Y';
					$boolValues = (array)Loc::getMessage('AETA_BOOLEAN_VALUES');
					?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_BOOLEAN]" value="N">
					<input class="option_field" type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_BOOLEAN]" value="Y" <?=$useBoolean ? 'checked' : ''?>>
					<?=Loc::getMessage('AETA_BOOLEAN_LABEL')?> <br>
					<div class="option_value <?=($useBoolean ? '' : 'hide')?>">
						<select name="PROFILE[FIELDS][<?=$i?>][BOOLEAN_VALUE]" class="btn-block">
							<?
							if($boolValues) {
								foreach($boolValues as $key => $value) {
									$selected = ($arField['BOOLEAN_VALUE'] == $value) ? ' selected' : '';
									?>
									<option value="<?=$key?>"<?=$selected?>><?=$value?></option><?
								}
								unset($key, $value, $selected);
							}
							?>
						</select>
					</div>
				</label>

				<label>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_CONDITIONS]" value="N">
					<input id="<?=$rowId?>_use_conditions_checkbox"
					       type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_CONDITIONS]"
					       onclick="showCatalogCondTree(this, <?=$i?>)"
					       value="Y"
					       data-id="<?=$id?>"
						 <?=($isCatalog ? '' : 'disabled')?>
						 <?=$useCondition?>>
					<?=Loc::getMessage('AETA_USE_CONDITIONS_LABEL')?>
				</label>

				<? /*if($useOffers):?>
					<label>
						<?$useOfferConditions  = $arField['USE_OFFER_CONDITIONS'] == 'Y' ? 'checked' : '';?>
						<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][USE_OFFER_CONDITIONS]" value="N">
						<input id="<?=$rowId?>_use_offer_conditions"
						       type="checkbox" name="PROFILE[FIELDS][<?=$i?>][USE_OFFER_CONDITIONS]"
						       onclick="showCatalogCondTree(this, <?=$i?>)"
						       value="Y"
						       data-id="<?=$id?>"
							 <?=$useOfferConditions?'checked':''?>>
						<?=Loc::getMessage('AETA_USE_OFFER_CONDITIONS_LABEL')?>
					</label>
				<?endif*/ ?>

			</div>

			<div id="<?=$rowId?>_condition" class="conditions-block <?=$hideCondition?>">
				<?
				if($isCatalog && $arField['USE_CONDITIONS'] == 'Y') {
					$obCond   = new CCatalogCondTree();
					$boolCond = $obCond->Init(
						 BT_COND_MODE_DEFAULT,
						 BT_COND_BUILD_CATALOG,
						 array(
								'FORM_NAME' => 'profile_form',
								'CONT_ID'   => $rowId . '_condition',
								'JS_NAME'   => 'JSCatCond_field_' . $rowId,
								'PREFIX'    => 'PROFILE[FIELDS][' . $i . '][CONDITIONS]',
						 )
					);
					if(!$boolCond) {
						if($ex = $APPLICATION->GetException())
							echo $ex->GetString() . "<br>";
					}
					else {
						$obCond->Show($arField['CONDITIONS']);
					}
				}
				?>
			</div>
			<? /*if($multiple):?>
					<input type="hidden" name="PROFILE[FIELDS][<?=$i?>][MULTIPLE]" value="<?=($multiple ? 'Y' : 'N')?>">
				<?endif*/ ?>
		</td>
	</tr>
	<?
	$i++;
	?>
<? endforeach ?>
	<?
	$html1 = ob_get_clean();
	ob_end_clean();

	if($isCustom) {
		$response = array(
			 'result'  => 'ok',
			 'message' => '',
			 'html'    => $html1,
		);
	}
	else {
		$response = array(
			 'result'  => 'ok',
			 'message' => '',
			 'items'   => array(
					array(
						 'id'   => '#profile_fields_table',
						 'html' => $html1,
					),
					array(
						 'id'   => '#offer_type_desc',
						 'html' => $arOfferType['DESCRIPTION'],
					),
			 ),
		);
	}
}

function getOfferFieldsSelect()
{
	global $POST, $response, $rowId, $type;

	ob_start();
	echo Tools::showOfferFieldsSelect($POST['IBLOCK_ID'], $type);
	$data = ob_get_clean();
	ob_end_clean();

	$response = array(
		 'result' => 'ok',
		 'html'   => $data,
	);
}

function getCatalogCondTree()
{
	global $APPLICATION, $POST, $response, $fieldId, $rowId, $key;

	$arOfferType      = Tools::getOfferType($POST['TYPE']);
	$arFieldCondition = $arOfferType['FIELDS'][ $fieldId ]['CONDITIONS'];

	if(Loader::includeModule('catalog')) {
		$obCond   = new CCatalogCondTree();
		$boolCond = $obCond->Init(
			 BT_COND_MODE_DEFAULT,
			 BT_COND_BUILD_CATALOG,
			 array(
					'FORM_NAME' => 'profile_form',
					'CONT_ID'   => $rowId . '_condition',
					'JS_NAME'   => 'JSCatCond_field_' . $rowId,
					'PREFIX'    => 'PROFILE[FIELDS][' . $key . '][CONDITIONS]',
			 )
		);

		if(!$boolCond) {
			if($ex = $APPLICATION->GetException())
				echo $ex->GetString();
		}
		else {
			$obCond->Show($arFieldCondition);
		}
	}

	$html = ob_get_clean();
	ob_end_clean();

	$response = array(
		 'result'  => 'ok',
		 'message' => '',
		 'items'   => array(
				array(
					 'id'   => '#' . $rowId . '_condition',
					 'html' => $html,
				),
		 ),
	);
}



///////////////////////////////////////////////////////////////////////////////
// Return ajax result
///////////////////////////////////////////////////////////////////////////////
if(!check_bitrix_sessid()) {
	$response = array(
		 'result'  => 'error',
		 'message' => 'sess expired',
	);
}

$APPLICATION->RestartBuffer();
$response['message'] = $APPLICATION->ConvertCharset($response['message'], LANG_CHARSET, 'UTF-8');

echo Json::encode($response);
die();