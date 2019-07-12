<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_js.php");
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/components/citrus/iblock.element.form/func.php");

__IncludeLang($_SERVER['DOCUMENT_ROOT'].'/bitrix/components/citrus/iblock.element.form/lang/'.LANGUAGE_ID.'/settings.php');

//if(!$USER->IsAdmin())
//	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED")); 

$obJSPopup = new CJSPopup('',
	array(
		'TITLE' => GetMessage('CIEE_POPUP_TITLE'),
		'SUFFIX' => 'ciee_element_form',
		'ARGS' => ''
	)
);

// ============== Init, input params =========================
$arErrors = Array();

$iblockID = IntVal($_REQUEST['iblockID']);
if ($iblockID <= 0)
	$arErrors[] = GetMessage("CIEE_IBLOCK_NOT_FOUND");

if (!CModule::IncludeModule('iblock'))
	$arErrors[] = GetMessage("CIEE_IBLOCK_MODULE_NOT_FOUND");

if (empty($arErrors) && CIBlock::GetPermission($iblockID) < 'X')
{
	$obJSPopup->ShowTitlebar();
	$obJSPopup->StartDescription('bx-edit-menu');
	$arErrors[] = GetMessage("CIEE_IBLOCK_PERMISSION_DENIED");
}


if (empty($arErrors))
{
	// ��������� ����
	$arFields = $arDefaultFields = CIEE_GetDefaultFields($iblockID, $bIncludeProperties = true);
	
	$arData = array();
	if ($_REQUEST['DATA'])
	{
		CUtil::JSPostUnescape();

		// ��� �������� �������, ��������� � ��������� �������� �� ��������� �����, ��������������� ������
		// � ����-������� �������� �� ���������� � ����������� ����� ����� � ��������������� ������
		if (defined('BX_UTF') && BX_UTF === true && function_exists('mb_strlen'))
			$_REQUEST['DATA'] = preg_replace('!s:(\d+):"(.*?)";!se', "'s:'.mb_strlen('$2', 'latin1').':\"$2\";'", $_REQUEST['DATA'] );
		if (CheckSerializedData($_REQUEST['DATA']))
		{
			$arData = unserialize($_REQUEST['DATA']);

			if (is_array($arData))
			{
				// ��������, ��� �� �������������� ����� � ������� ��������
				foreach ($arData as $field => &$fieldData)
				{
					if (!array_key_exists($field, $arFields))
					{
						unset($arData[$field]);
						continue;
					}
				}
				// ������� ����, ������� ��� � �������
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

// ������������� � ������� < 11.0.10
$strJS = version_compare(SM_VERSION, "11.0.0", ">=") ? CUtil::InitJSCore('jquery', $bReturn = true) : '';
if (strlen($strJS) > 0)
{
	echo $strJS;
}
else
{
	?><script type="text/javascript" src="/bitrix/components/citrus/iblock.element.form/settings/jquery-1.7.1.min.js"></script><?
}
?>

<script type="text/javascript" src="/bitrix/components/citrus/iblock.element.form/settings/settings_load.js"></script>
<script type="text/javascript">
BX.loadCSS('/bitrix/components/citrus/iblock.element.form/settings/settings.css');
var arFieldData = <?echo is_array($arData) && count($arData) > 0 ? CUtil::PhpToJsObject($arData) : '{}'?>;
var arDefaultFields = <?=CUtil::PhpToJsObject($arDefaultFields)?>;
window._global_BX_UTF = <?echo defined('BX_UTF') && BX_UTF == true ? 'true' : 'false'?>;
BX.message({
	ciee_delete: '<?echo CUtil::JSEscape(GetMessage("CIEE_DELETE"))?>',
	ciee_add_field: '<?echo CUtil::JSEscape(GetMessage("CIEE_ADD_FIELD"))?>',
	ciee_no_fields_to_add: '<?echo CUtil::JSEscape(GetMessage("CIEE_NO_FIELDS_TO_ADD"))?>'
});

jsCIEESettings.init(arFieldData, arDefaultFields, $('#ciee-form-fields tbody'), $('#ciee-form-footer'));

BX.ready(function() {
	window.setTimeout(function() {
	    // Initialise the table
	    $("#ciee-form-fields").tableDnD({
	      onDragClass: "dragging-td",
//	      dragHandle: "drag-handle"
	    });
	    
	    $("#ciee-form-fields tr").hover(function() {
	          $(this.cells[0]).addClass('showDragHandle');
	    }, function() {
	          $(this.cells[0]).removeClass('showDragHandle');
	    });
	}, 500);
});
</script>
<form name="bx_popup_form_ciee_element_form">
<?
$obJSPopup->ShowTitlebar();
$obJSPopup->StartDescription('bx-edit-menu');

?>
	<p><b><?echo GetMessage('CIEE_POPUP_WINDOW_TITLE')?></b></p>
	<p class="note"><?echo GetMessage('CIEE_POPUP_WINDOW_DESCRIPTION')?></p>
<?

// ============== Content  =========================
$obJSPopup->StartContent();

	?>
	<table class="ciee-form-fields" id="ciee-form-fields">
	<thead>
		<tr>
			<th><?=GetMessage("CIEE_T_FIELD")?></th>
			<th><?=GetMessage("CIEE_T_TITLE")?></th>
			<th><?=GetMessage("CIEE_T_REQUIRED")?></th>
			<th><?=GetMessage("CIEE_T_TOOLTIP")?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
	</tbody>
	<tfoot>
		<tr><td colspan="5">
			<div id="ciee-form-footer"></div>
		</td></tr>
	</tfoot>
	</table>
	
	<?

// ============== Buttons =========================
$obJSPopup->StartButtons();
?>
<input type="submit" value="<?echo GetMessage('CIEE_SUBMIT')?>" onclick="return jsCIEESettings.__saveChanges();"/>
<?
$obJSPopup->ShowStandardButtons(array('cancel'));
$obJSPopup->EndButtons();
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin_js.php");?>