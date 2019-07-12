<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/citrus/realty.send_event/func.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/citrus/realty.send_event/lang/'.LANGUAGE_ID.'/settings.php');

//if(!$USER->IsAdmin())
//	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED")); 

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage('CSE_POPUP_TITLE'),
		'SUFFIX' => 'cse_form',
		'ARGS' => ''
	)
);

// ============== Init, input params =========================
$arErrors = Array();

$eventType = trim($_REQUEST["event"]);
if (strlen($eventType) <= 0)
	$arErrors[] = GetMessage("CSE_EVENT_TYPE_NOT_FOUND");

if (empty($arErrors))
{
	// доступные поля
	$arFields = $arDefaultFields = CSE_GetFields($eventType);
	
	$arData = array();
	if ($_REQUEST['DATA'])
	{
		CUtil::JSPostUnescape();

		// для тиражных решений, собранных в кодировке отличной от кодировки сайта, сериализованный массив
		// с демо-данными окажется не корректным — пересчитаем длину строк в сериализованной строке
		if (defined('BX_UTF') && BX_UTF === true && function_exists('mb_strlen'))
			$_REQUEST['DATA'] = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.mb_strlen('$2', 'latin1').':\"$2\";'", $_REQUEST['DATA'] );
		if (CheckSerializedData($_REQUEST['DATA']))
		{
			$arData = unserialize($_REQUEST['DATA']);

			if (is_array($arData))
			{
				// проверим, нет ли несуществующих полей в массиве настроек
				foreach ($arData as $field => &$fieldData)
				{
					if (!array_key_exists($field, $arFields))
					{
						unset($arData[$field]);
						continue;
					}
				}
				// добавим поля, которых нет в массиве
				$arMissingFields = 	array_diff_key($arFields, $arData);
				foreach ($arMissingFields as $code => $field)
				{
					$field['ACTIVE'] = false;
					$arData[$code] = $field;
				}
			}
			else
			{
				$arData = $arFields;
			}
		}
	}
	if (empty($arData))
		$arData = $arFields;
}

if (count($arErrors) > 0)
	$obJSPopup->ShowError(implode('<br/>', $arErrors));

// ============== JS init =========================

// совместимость с битрикс < 11.0.10
$strJS = version_compare(SM_VERSION, "11.0.0", ">=") ? CUtil::InitJSCore('jquery', $bReturn = true) : '';
if (strlen($strJS) > 0)
{
	echo $strJS;
}
else
{
	?><script type="text/javascript" src="/bitrix/components/citrus/realty.send_event/settings/jquery-1.7.1.min.js"></script><?
}
?>

<script type="text/javascript" src="/bitrix/components/citrus/realty.send_event/settings/settings_load.js"></script>
<script type="text/javascript">
BX.loadCSS('/bitrix/components/citrus/realty.send_event/settings/settings.css');
var arFieldData = <?=is_array($arData) && count($arData) > 0 ? CUtil::PhpToJsObject($arData) : '{}'?>;
var arDefaultFields = <?=CUtil::PhpToJsObject($arDefaultFields)?>;
window._global_BX_UTF = <?=defined('BX_UTF') && BX_UTF == true ? 'true' : 'false'?>;
BX.message({
	cse_delete: '<?=CUtil::JSEscape(GetMessage("CSE_DELETE"))?>',
	cse_add_field: '<?=CUtil::JSEscape(GetMessage("CSE_ADD_FIELD"))?>',
	cse_no_fields_to_add: '<?=CUtil::JSEscape(GetMessage("CSE_NO_FIELDS_TO_ADD"))?>'
});

BX.ready(function() {
	jsCSESettings.init(arFieldData, arDefaultFields, $('#cse-form-fields tbody'), $('#cse-form-footer'));
	window.setTimeout(function() {
	    // Initialise the table
		$("#cse-form-fields").tableDnD({
	      onDragClass: "dragging-td",
//	      dragHandle: "drag-handle"
	    });
	    
	    $("#cse-form-fields tr").hover(function() {
	          $(this.cells[0]).addClass('showDragHandle');
	    }, function() {
	          $(this.cells[0]).removeClass('showDragHandle');
	    });
	}, 500);
});
</script>
<form name="bx_popup_form_cse_form">
<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-edit-menu');

?>
	<p><b><?echo GetMessage('CSE_POPUP_WINDOW_TITLE')?></b></p>
	<p class="note"><?echo GetMessage('CSE_POPUP_WINDOW_DESCRIPTION')?></p>
<?

// ============== Content  =========================
$obJSPopup->StartContent();

	?>
	<table class="cse-form-fields" id="cse-form-fields">
	<thead>
		<tr>
			<th><?=GetMessage("CSE_T_FIELD")?></th>
			<th><?=GetMessage("CSE_T_TITLE")?></th>
			<th><?=GetMessage("CSE_T_REQUIRED")?></th>
			<th><?=GetMessage("CSE_T_TOOLTIP")?></th>
			<th>E-mail</th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="6"><div id="cse-form-footer"></div></td>
		</tr>
	</tfoot>
	</table>
	<?

// ============== Buttons =========================
$obJSPopup->StartButtons();
?>
<input type="submit" value="<?echo GetMessage('CSE_SUBMIT')?>" onclick="return jsCSESettings.__saveChanges();"/>
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>