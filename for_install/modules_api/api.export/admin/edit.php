<?
/**
 * Bitrix vars
 *
 * @var CUser $USER
 * @var CMain $APPLICATION
 *
 */
use Bitrix\Main\Loader,
	 Bitrix\Main\Application,
	 Bitrix\Main\Type\DateTime,
	 Bitrix\Main\Localization\Loc;

define("ADMIN_MODULE_NAME", "api.export");
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
Loc::loadMessages(__FILE__);

CJSCore::Init(array('jquery2', 'ajax'));

$rights = $APPLICATION->GetGroupRight(ADMIN_MODULE_NAME);
if($rights < 'W')
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));

if(!Loader::includeModule(ADMIN_MODULE_NAME))
	$APPLICATION->AuthForm(Loc::getMessage('ACCESS_DENIED'));


use \Api\Export\ProfileTable;
use \Api\Export\Converter;
use \Api\Export\Tools;


//Лэнги полей
$arFieldTitle = array();

$columns = ProfileTable::getEntity()->getFields();
/** @var \Bitrix\Main\Entity\Field $field */
foreach($columns as $code => $column) {
	$arFieldTitle[ $column->getName() ] = $column->getTitle();
}


$context      = Application::getInstance()->getContext();
$documentRoot = Application::getDocumentRoot();
$request      = $context->getRequest();
$lang         = $context->getLanguage();

$save         = trim($request->get('save'));
$apply        = trim($request->get('apply'));
$action       = trim($request->get('action'));
$update       = trim($request->get('update'));

$id           = intval($request->get('ID'));
$bCopy        = ($action == "copy");
$bUpdate      = ($update == "Y");
$bSale        = Loader::includeModule('sale');
$bCatalog     = Loader::includeModule('catalog');
$bIblock      = Loader::includeModule('iblock');
$bCurrency    = Loader::includeModule('currency');
$hint         = '/bitrix/js/main/core/images/hint.gif';
$errorMessage = '';


$aTabs      = (array)Loc::getMessage("AEAE_TABS");
$tabControl = new CAdminForm("profile", $aTabs);

if($request->isPost() && $bUpdate && $rights >= "W" && check_bitrix_sessid()) {
	$POST = $_POST['PROFILE'];

	//PREPARE CURRENCY
	if($POST['CURRENCY']) {
		foreach($POST['CURRENCY'] as $key => $arCurrency) {
			if($arCurrency['ACTIVE'] != 'Y')
				unset($POST['CURRENCY'][ $key ]);
		}
	}
	//\\PREPARE CURRENCY

	//ELEMENTS_CONDITION
	if($bCatalog && $POST['ELEMENTS_CONDITION']) {
		$obCond             = new CCatalogCondTree();
		$boolCond           = $obCond->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array());
		$POST['ELEMENTS_CONDITION'] = $obCond->Parse($POST['ELEMENTS_CONDITION']);
	}
	else {
		$POST['ELEMENTS_CONDITION'] = array();
	}

	//ELEMENTS_CONDITION
	if($bCatalog && $POST['OFFERS_CONDITION']) {
		$obCond             = new CCatalogCondTree();
		$boolCond           = $obCond->Init(BT_COND_MODE_PARSE, BT_COND_BUILD_CATALOG, array());
		$POST['OFFERS_CONDITION'] = $obCond->Parse($POST['OFFERS_CONDITION']);
	}
	else {
		$POST['OFFERS_CONDITION'] = array();
	}

	//PREPARE OFFER_XML FIELD CONDITIONS
	if($POST['FIELDS']) {
		foreach($POST['FIELDS'] as $key => &$field) {
			if($field['USE_CONDITIONS'] == 'Y' && $field['CONDITIONS']) {
				$field['CONDITIONS'] = $obCond->Parse($field['CONDITIONS']);
			}
			else {
				$field['CONDITIONS'] = array();
			}
		}
	}

	if(!isset($POST['ELEMENTS_FILTER']))
		$POST['ELEMENTS_FILTER'] = array();

	if(!isset($POST['OFFERS_FILTER']))
		$POST['OFFERS_FILTER'] = array();

	if(!isset($POST['DELIVERY']))
		$POST['DELIVERY'] = array();

	if(!isset($POST['UTM_TAGS']))
		$POST['UTM_TAGS'] = array();
	if(serialize($POST['UTM_TAGS']) == 'a:2:{s:4:"NAME";a:1:{i:0;s:0:"";}s:5:"VALUE";a:1:{i:0;s:0:"";}}')
		$POST['UTM_TAGS'] = array();

	if(!isset($POST['DIMENSIONS']))
		$POST['DIMENSIONS'] = '';


	$POST['MODIFIED_BY'] = intval($USER->GetID());

	//Обрабатываем необходимые поля перед сохранением
	ProfileTable::encodeFields($POST);


	//Save form data
	if($id && !$bCopy)
		$res = ProfileTable::update($id, $POST);
	else {
		$id  = ProfileTable::add($POST)->getId();
		$res = ($id > 0);
	}

	if($res) {
		if($apply != "")
			LocalRedirect("/bitrix/admin/api_export_edit.php?ID=" . $id . "&mess=ok&lang=" . LANG . "&" . $tabControl->ActiveTabParam());
		else
			LocalRedirect("/bitrix/admin/api_export_list.php?lang=" . LANG);
	}
	else {
		$bVarsFromForm = true;
	}
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

//START VIEW

if($errorMessage)
	\CAdminMessage::ShowMessage($errorMessage);


if($id) {
	if($profile = ProfileTable::getRowById($id)) {
		ProfileTable::decodeFields($profile);
		$APPLICATION->SetTitle($profile['NAME']);
	}
}
if(empty($profile)) {
	$profile = Tools::getProfileDefaults();
	$APPLICATION->SetTitle(Loc::getMessage("AEAE_PAGE_TITLE_ADD"));
}


//Кнопки = Добавить/Копировать/Удалить
$aMenu = array(
	 array(
			"TEXT" => Loc::getMessage("MAIN_ADMIN_MENU_LIST"),
			"LINK" => "api_export_list.php?lang=" . LANG,
			"ICON" => "btn_list",
	 ),
);
if($id && !$bCopy) {
	$aMenu[] = array("SEPARATOR" => "Y");
	$aMenu[] = array(
		 "TEXT" => Loc::getMessage("AEAE_ADMIN_MENU_ADD"),
		 "LINK" => "api_export_edit.php?lang=" . LANG,
		 "ICON" => "btn_new",
	);
	$aMenu[] = array(
		 "TEXT" => Loc::getMessage("AEAE_ADMIN_MENU_COPY"),
		 "LINK" => "api_export_edit.php?ID=" . $id . "&amp;action=copy&amp;lang=" . LANG,
		 "ICON" => "btn_copy",
	);
	$aMenu[] = array(
		 "TEXT" => Loc::getMessage("AEAE_ADMIN_MENU_DELETE"),
		 "LINK" => "javascript:if(confirm('" . Loc::getMessage("AEAE_ADMIN_MENU_CONFIRM_DELETE") . "'))window.location='api_export_list.php?ID=" . $id . "&action=delete&lang=" . LANG . "&" . bitrix_sessid_get() . "';",
		 "ICON" => "btn_delete",
	);
}
$context = new CAdminContextMenu($aMenu);
$context->Show();


//tab1
$profile['FILE_PATH'] = Tools::getHttpFilePath($profile['FILE_PATH']);
$arCharset = (array)Loc::getMessage('AEAE_PROFILE_CHARSET');

//tab2
$arCurrency      = Tools::getCurrency();
$arCurrencyRates = Tools::getCurrencyRates();
$arPriceTypes    = Tools::getPriceTypes();

//tab3
$arCatalogs        = Tools::getCatalogs($profile['USE_CATALOG'] == 'Y');
$arCatalogSections = Tools::getCatalogSections($profile['IBLOCK_ID'], $profile['USE_SUBSECTIONS'] == 'Y');

//tab5
$arTypes = Tools::getOfferTypes();

$tabControl->BeginPrologContent();
?>
	<style type="text/css">
		#profile_form div[id*="api_iblock_"]{ display: inline-block; vertical-align: top; margin: 0 0 15px }
		#profile_form .hide{ display: none; }
		#profile_form .condition-block{ margin: 4px 0; }
		#profile_form .offer-type-field > td{ position: relative; padding: 10px; border-bottom: 5px solid #D2D5D5;}
		#profile_form .offer-type-field > td:first-child{padding: 20px 10px 20px 0 !important;}
		#profile_form .offer-type-field > td:not(:first-child){width: 30%}
		#profile_form #tab5 .api-icon-remove{ cursor: pointer; position: absolute; top: 15px; right: 5px; }
		#profile_form [class*="_block"]{ margin: 5px 0 }
		#profile_form #tr_CONDITIONS td{ padding-top: 10px }
		#profile_form .copy_row{ margin: 10px 0 }
		#profile_form .copy_row > div{ display: inline-block }
		#profile_form .copy_row .controls{ margin-left: 10px; position: relative }
		#profile_form .copy_row .controls i{ vertical-align: middle; margin-right: 5px }

		#profile_form .field-row + .field-row{margin-top: 15px}
		#profile_form .type_row + .value_row{margin-top: 5px}
		#profile_form .options_block label{ display: block }
		#profile_form .options_block .btn-block{margin: 5px 0 10px}

		#profile_form .btn-block{display: block; width: 100%;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;}
		#profile_form .td-condition .btn-block{max-width: 300px;}

		#profile_form .adm-btn-icon{height: 26px; line-height: 1}
		#profile_form .adm-btn-icon:before{margin: 0 !important; height: 12px; width: 12px}
		#profile_form .adm-btn-icon:active{padding: 0 8px !important;}
		#profile_form .adm-btn[disabled]{cursor: default;opacity: .5;}

		#profile_fields_table .field-row{position: relative; padding:10px 75px 10px 10px;background-color: #f7f7f9;border: 1px solid #e1e1e8;border-radius: 4px;}
		#profile_fields_table .field-row .controls{position: absolute;top: 50%;right: 10px;margin-top: -15px;}

		.api-label{display: inline-block;padding: 0 15px;background: #1e87f0;line-height: 1.5;font-size: 12px;color: #fff;vertical-align: middle;white-space: nowrap;border-radius: 2px;}
		.api-label-danger{background-color: #f0506e;color: #fff;}
		.api-label-success {background-color: #32d296;color: #fff;}
		.api-label-warning {background-color: #faa05a;color: #fff;}
		.api-label-secondary {background-color: #545b62;color: #fff;}

		/* icons */
		.api-icon-warning{
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAA2hpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVzb3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1NOk9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDowMjgwMTE3NDA3MjA2ODExODA4M0U4NTZDQzc2NjBGNyIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo5RjE1NDAwRTg0QkQxMUUyOUQzMkYyOEE5MkE5NjA5MCIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo5RjE1NDAwRDg0QkQxMUUyOUQzMkYyOEE5MkE5NjA5MCIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChNYWNpbnRvc2gpIj4gPHhtcE1NOkRlcml2ZWRGcm9tIHN0UmVmOmluc3RhbmNlSUQ9InhtcC5paWQ6MDM4MDExNzQwNzIwNjgxMTgwODNFODU2Q0M3NjYwRjciIHN0UmVmOmRvY3VtZW50SUQ9InhtcC5kaWQ6MDI4MDExNzQwNzIwNjgxMTgwODNFODU2Q0M3NjYwRjciLz4gPC9yZGY6RGVzY3JpcHRpb24+IDwvcmRmOlJERj4gPC94OnhtcG1ldGE+IDw/eHBhY2tldCBlbmQ9InIiPz7BPwTGAAAAtUlEQVR42mL8n5bGQC5gYqAA4NMcBsQfgdidVM0CQDwFiPmAeDoQc5CiuR6IRaFsRSAuJVazBhBng1kzZ8LEKoBYjhjNE4GYFU2MC4h7CGkOAGI3HF4JBWJHXJpBgdJHIHYmATELNs2l0MBBgPR0dM06QJyGrlkOGiioABFgyKAJiIWRNXdBA4UYANLYDGIwAtO2IZA+C2KTkDL/ArE2yGZ2IH5PYrJ+C8RsoJA7AfMDqQAgwADKNRgFaderxgAAAABJRU5ErkJggg==");
			display: inline-block;
			width: 15px;
			height: 15px;
			margin-bottom: -2px;
		}
		.api-icon-remove{
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAkAAAAICAYAAAArzdW1AAAAVklEQVR42nWOgQnAMAgE3aCjOJEDuY3DZJ8rqZYipg8KPgen7GACvpAKLFCBgCxcYfeSYAHVFUhYHqdRR96w/A/40hRNzXUAJpiQdcVQB+QuYPxo8XQ33NCTVnhoHP8AAAAASUVORK5CYII=");
			display: inline-block;
			width: 9px;
			height: 8px;
			cursor: pointer;
		}
		.api-icon-add{
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAJCAYAAAALpr0TAAAAPklEQVR42mNgQAL/rzz9r19+9z8DEMfu+f+fARegjsINMyESePHMl/+JU9jx9CHD/z2PCCrUX/XhP8PA+RoAA5+PGCRQdN8AAAAASUVORK5CYII=");
			display: inline-block;
			width: 10px;
			height: 9px;
			cursor: pointer;
		}

		.api-icon-info{
			background: url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAAAUElEQVR42mNgoBQELT7jAMTXgPgdEfgWENsja74DxP9JwDeRNWNVBAK45GirGR/GqxkGBsbZA6MZGdA0tElNYXeQNbsB8X0iNT4FYheKMxQA+y/YhU0oGbsAAAAASUVORK5CYII=");
			display: inline-block;
			width: 15px;
			height: 15px;
			margin-bottom: -2px;
		}
		#profile_form :not(pre) > code{
			border: none;
			display: inline-block;
			margin: 3px 0;
			padding: 2px 4px;
			color: #c7254e;
			background: #f9f2f4;
			white-space: nowrap;
			font-family: Menlo, Monaco, Consolas, "Courier New", monospace, serif;
			-webkit-border-radius: 4px;
			-moz-border-radius: 4px;
			border-radius: 4px;
		}
	</style>
	<script type="text/javascript">
		$(function () {

			//tab2
			$(document).on('click', '.copy_inner .adm-btn-add', function () {
				var curRow = $(this).closest('.copy_row');
				var cloneRow = curRow.clone();
				cloneRow.find('input, select').val('');
				cloneRow.insertAfter(curRow);
			});
			$(document).on('click', '.copy_inner .adm-btn-delete', function () {
				var inner = $(this).closest('.copy_inner');
				var row = $(this).closest('.copy_row');
				if($(inner).find('.copy_row').length > 1){
					$(row).remove();
				}
			});

			//tab3
			$(document).on('change', 'input[name="PROFILE[USE_CATALOG]"]', function () {
				execAjax('changeUseCatalog');
			});
			$(document).on('change', 'input[name="PROFILE[USE_SUBSECTIONS]"]', function () {
				execAjax('changeUseSubsections');
			});

			$('#api_iblock_type_id').on('change', 'select', function () {
				$('#api_iblock_id select, #api_iblock_section_id select').hide();
				execAjax('changeIblockTypeId');
			});
			$('#api_iblock_id').on('change', 'select', function () {
				$('#api_iblock_section_id select').hide();
				execAjax('changeIblockId');
			});

			//tab5
			$(document).on('change', 'select[name="PROFILE[TYPE]"]', function () {
				execAjax('changeOfferType');
			});

			$(document).on('click', '.option_field', function () {
				$(this).parent('label').find('.option_value').toggle();
			});


			$(document).on('click', '#profile_fields_table .controls .adm-btn-add', function () {
				var curRow = $(this).closest('.field-row');
				var cloneRow = curRow.clone();
				//cloneRow.find('select').val('');
				cloneRow.find('.adm-btn[disabled]').removeAttr('disabled');
				cloneRow.insertAfter(curRow);
			});
			$(document).on('click', '#profile_fields_table .controls .adm-btn-delete', function () {
				var inner = $(this).closest('.td-condition');
				var row = $(this).closest('.field-row');
				if($(inner).find('.field-row').length > 1){
					$(row).remove();
				}
			});
		});

		$(window).on('load',function(){
			$('select[name="PROFILE[TYPE]"]').trigger('change');
		});

		function getDefaultData() {
			var obData = {
				'PROFILE[ID]': $('input[type="hidden"][name="ID"]').val(),
				'PROFILE[IBLOCK_TYPE_ID]': $('select[name="PROFILE[IBLOCK_TYPE_ID]"]').val(),
				'PROFILE[IBLOCK_ID]': $('select[name="PROFILE[IBLOCK_ID]"]').val(),
				'PROFILE[SECTION_ID][]': $('select[name="PROFILE[SECTION_ID][]"]').val(),
				'PROFILE[TYPE]': $('select[name="PROFILE[TYPE]"]').val(),
				'PROFILE[USE_CATALOG]': typeof $('input[name="PROFILE[USE_CATALOG]"]:checked').val() === "undefined" ? '' : 'Y',
				'PROFILE[USE_SUBSECTIONS]': typeof $('input[name="PROFILE[USE_SUBSECTIONS]"]:checked').val() === "undefined" ? '' : 'Y',
				'sessid': BX.bitrix_sessid()
			};

			return obData;
		}

		function execAjax(action, data) {

			if (typeof data === 'undefined') {
				data = getDefaultData();
			}
			data['exec_action'] = action;

			BX.showWait('wait1');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: '/bitrix/admin/api_export_ajax.php',
				data: data,
				async: true,
				error: function (request, error) {
					if (error.length)
						alert('Error! ' + error);
				},
				success: function (data) {
					BX.closeWait('wait1');

					if (data.result == 'ok') {
						for (var key in data.items) {
							var item = data.items[key];
							$(item.id).html(item.html);
						}
					}
				}
			});
		}

		function getOfferFieldsSelect(select, rowId) {

			var value_row = $(select).parents('.field-row').find('.value_row');

			if (select.value === 'NONE') {
				value_row.hide();
			} else {
				value_row.show();

				var data = getDefaultData();
				data['rowId'] = rowId;
				data['type'] = $(select).val();
				data['exec_action'] = 'getOfferFieldsSelect';

				BX.showWait('wait1');
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: '/bitrix/admin/api_export_ajax.php',
					data: data,
					async: true,
					error: function (request, error) {
						if (error.length)
							alert('Error! ' + error);
					},
					success: function (data) {
						BX.closeWait('wait1');

						if (data.result == 'ok') {
							$(value_row).find('select').html(data.html);
						}
					}
				});
			}
		}

		function showCatalogCondTree(_this, key) {
			var fieldId = $(_this).attr('data-id');
			var rowId = 'row_' + key;

			var data = getDefaultData();
			data['fieldId'] = fieldId;
			data['rowId'] = rowId;
			data['key'] = key;

			execAjax('getCatalogCondTree', data);

			if ($(_this).prop('checked')) {
				$('#' + rowId + '_condition').removeClass('hide');
			} else {
				$('#' + rowId + '_condition').addClass('hide');
			}
		}

		function customFieldAdd(_this) {
			var customId = ($('#profile_fields_table > tr').length);

			var data = getDefaultData();
			data['isCustom'] = 1;
			data['customId'] = customId;
			data['exec_action'] = 'changeOfferType';

			BX.showWait('wait1');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: '/bitrix/admin/api_export_ajax.php',
				data: data,
				async: true,
				error: function (request, error) {
					if (error.length)
						alert('Error ' + error);
				},
				success: function (data) {
					BX.closeWait('wait1');

					if (data.result == 'ok') {
						$('#profile_fields_table tr:last').after(data.html);
					}
					else {
						alert('Error create custom field');
					}
				}
			});

			return false;
		}

		function customFieldRemove(_this) {
			$(_this).parents('.offer-type-field').fadeOut(200, function () {
				$(this).remove()
			});
		}

	</script>
	<div id="wait1" style="position: fixed; float: right; width: 100%; right: 0;"></div>
<?
$tabControl->EndPrologContent();

$tabControl->BeginEpilogContent();
?>
<?=bitrix_sessid_post()?>
	<input type="hidden" name="update" value="Y">
	<input type="hidden" name="lang" value="<?=$lang;?>">
<? if(!$bCopy): ?>
	<input type="hidden" name="ID" value="<?=$id;?>">
<? endif ?>
<?
$tabControl->EndEpilogContent();


/////////////////////////////////////////////////////////////////////////////////////////
/// Начало формы
/////////////////////////////////////////////////////////////////////////////////////////
$tabControl->Begin(array('FORM_ACTION' => $APPLICATION->GetCurPage() . "?lang=" . $lang));

foreach($aTabs as $aTab) {
	$tabControl->BeginNextFormTab();
	include 'tabs/' . $aTab['DIV'] . '.php';
}
$tabControl->Buttons(array(
	 "disabled" => ($rights < "W"),
	 "back_url" => "api_export_list.php?lang=" . LANG,
));
$tabControl->Show();

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_admin.php');
?>