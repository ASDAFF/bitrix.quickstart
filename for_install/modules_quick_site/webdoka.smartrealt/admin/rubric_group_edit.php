<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';
$itemId = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);

$B_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($B_RIGHT <= 'R')
{
	$APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

if (!CModule::IncludeModule($module_id))
{
	$APPLICATION->AuthForm(GetMessage('B_ERROR_LOAD_MODULE'));
}

$sListPage = 'smartrealt_rubric_group_list.php';
$sEditPage = 'smartrealt_rubric_group_edit.php';

$obRubricGroup = new SmartRealt_RubricGroup();
$arFields = array();

if ($itemId && $_POST['Update']!='Y'/*&& $REQUEST_METHOD == 'GET'*/) 
{
	$rsRubricGroup = $obRubricGroup->GetList(array('Id' => $itemId, 'ALL'=>'Y'));
	if (!($arFields = $rsRubricGroup->Fetch()))
	{
		$itemId = '';
	}
}

if ($itemId)
{               
	extract($arFields, EXTR_PREFIX_ALL, 'f');
}
else
{
	$f_AddNext = (isset($_REQUEST['AddNext'])?'Y':'N'); 
	$f_RubricGroupSort = 100;
}

$eSaveErrors = new CAdminException();
$eDeleteErrors = new CAdminException();
$eCopyErrors = new CAdminException();

if (isset($_GET['delete']) && $B_RIGHT == 'W')
{
	$sDelId = $_GET['delete'];
	
	if ($obRubricGroup->IsDelete($sDelId))
	{
		if ($obRubricGroup->Delete($sDelId))
		{
			LocalRedirect($sListPage . '?lang=' . LANGUAGE_ID);
			exit();
		}
		else 
		{
			$eDeleteErrors->AddMessage(
				array(
					'id' => 'delete_rubric_group',
					'text' => GetMessage('B_DELETE_ITEM_ERROR')
				)
			);
		}
	}
	else
	{
		$eDeleteErrors->AddMessage(
			array(
				'id' => 'delete_rubric_group',
				'text' => GetMessage("B_DELETE_IMPOSSIBLE")
			)
		);
	}
}

if (isset($_GET['copy']) && $B_RIGHT > 'R')
{
	$sCopyId = $_GET['copy'];
	if ($sNewId = $obRubricGroup->CopyObject($sCopyId))
	{
		LocalRedirect($sEditPage . '?lang=' . LANGUAGE_ID . '&id=' . $sNewId);
		exit();
	}
	else
	{
		$eCopyErrors->AddMessage(
			array(
				'id' => 'copy_rubric_group',
				'text' => GetMessage("B_COPY_ERROR_TEXT")
			)
		);
	}
}

$arTabs = array(
	array('DIV' => 'rubric_group_edit', 
		'TAB' => $itemId ? GetMessage('B_EDIT_TAB') : GetMessage('B_ADD_TAB'), 
		'TITLE' => $itemId ? GetMessage('B_EDIT_TITLE') : GetMessage('B_ADD_TITLE')),    
);

$tabControl = new CAdminTabControl("tabControl", $arTabs);

if (($REQUEST_METHOD == 'POST') && $_POST['Update']=='Y' && $B_RIGHT > 'R')
{
	foreach ($_POST as $sField=>$sVal)
		$_POST[$sField] = trim($sVal);             
	
	$_POST['Active'] = isset($_POST['Active']) ? 'Y' : 'N';
	
	$eSaveErrors = $obRubricGroup->CheckFields($_POST);
    
	if (count($eSaveErrors->GetMessages()) == 0)
	{
		global $DB;
		$DB->StartTransaction();
		
		if (!$itemId)
		{
			$itemId = $obRubricGroup->Add($_POST);
			
			if (!$itemId)
			{
				$DB->Rollback();
				$eSaveErrors->AddMessage(
					array(
						'id' => 'insert_error',
						'text' => GetMessage('B_INSERT_ERROR')
					)
				);
			}
		}
		else 
		{
			if (!$obRubricGroup->Add($_POST, $itemId))
			{
				$DB->Rollback();
				$eSaveErrors->AddMessage(
					array(
						'id' => 'update_error',
						'text' => GetMessage('B_UPDATE_ERROR')
					)
				);
			}
		}
		
		$DB->Commit();
                       
		if (count($eSaveErrors->GetMessages()) == 0)
		{
			if (isset($_POST['save']))
			{
				if (isset($_POST['NEW_AFTER_SAVE']))
				{
					LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&id=&AddNext=Y&' . $tabControl->ActiveTabParam());
				}
				else 
				{
					LocalRedirect($sListPage . '?lang=' . LANGUAGE_ID);
				}
			}
			else 
			{
				LocalRedirect($APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID . '&id=' . $itemId . '&' . $tabControl->ActiveTabParam());
			}
			
			exit();
		}
	}
    foreach ($_POST as $key=>$val)
    {
        ${'f_'.$key} = $val;
    }
    //extract($_POST, EXTR_PREFIX_ALL, 'f');  
}

$APPLICATION->SetTitle($itemId ? GetMessage('B_PAGE_EDIT_TITLE') : GetMessage('B_PAGE_ADD_TITLE'));     
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
SmartRealt_Common::CheckToken();
if (count($eDeleteErrors->GetMessages()) > 0)
{
	$message = new CAdminMessage(GetMessage('B_DELETE_ERROR'), $eDeleteErrors);
	echo $message->Show();
}

if (count($eSaveErrors->GetMessages()) > 0)
{
	$message = new CAdminMessage(GetMessage('B_SAVE_ERROR'), $eSaveErrors);
	echo $message->Show();
}

if (count($eCopyErrors->GetMessages()) > 0)
{
	$message = new CAdminMessage(GetMessage('B_COPY_ERROR'), $eCopyErrors);
	echo $message->Show();
}

$arContextMenu = array(
	array(
		'TEXT'	=> GetMessage('B_LIST_BTN'),
		'LINK'	=> $sListPage . '?lang=' . LANGUAGE_ID,
		'TITLE'	=> GetMessage('B_LIST_BTN_TITLE'),
		'ICON'	=> 'btn_list'
	),
);
if ($itemId)
{
	if ($B_RIGHT > 'R')
	{
		$arContextMenu[] = array(
			'TEXT'	=> GetMessage('B_ADD_BTN'),
			'LINK'	=> $APPLICATION->GetCurPage() . '?id=&lang=' . LANGUAGE_ID,
			'TITLE'	=> GetMessage('B_ADD_BTN_TITLE'),
			'ICON'	=> 'btn_new'
		);
		$arContextMenu[] = array(
			'TEXT'	=> GetMessage('B_COPY_BTN'),
			'LINK'	=> $APPLICATION->GetCurPageParam('copy=' . $itemId, array('delete')), 
			'TITLE'	=> GetMessage('B_COPY_BTN_TITLE'),
			'ICON'	=> 'btn_copy'
		);
	}
	if ($B_RIGHT == 'W')
	{
		$arContextMenu[] = array(
			'TEXT'			=> GetMessage('B_DELETE_BTN'),
			'LINK'			=> $APPLICATION->GetCurPageParam('delete=' . $itemId, array('delete')),
			'TITLE'			=> GetMessage('B_DELETE_BTN_TITLE'),
			'ICON'			=> 'btn_delete',
			'LINK_PARAM'	=> 'onclick="return confirm(\'' . GetMessage('B_DELETE_CONFIRM', array('#NAME#' => addslashes($f_RubricGroupName))) . '\')"'
		);
	}
}	    

$oMenu = new CAdminContextMenu($arContextMenu);
$oMenu->Show();
?>

<form name='edit_rubric_group_form' method='POST' action="?id=<?=$itemId?>&lang=<?=LANGUAGE_ID?>">
<input type="hidden" name="id" value="<?=$itemId?>">
<input type="hidden" name="Update" value="Y">

<?php
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<col width="40%" />
<col />

<tr>
	<td class="adm-detail-content-cell-l"><label for="Active"><?=GetMessage('B_ACTIVE')?>:</label></td>
	<td class="adm-detail-content-cell-r"><input type="checkbox" name="Active" id="Active" <?=(isset($f_Active) && $f_Active == 'Y') || !$itemId ? 'checked' : '' ?> /></td>
</tr>
<tr>
	<td class="adm-detail-content-cell-l"><span class="required">*</span> <label for="Name"><?=GetMessage('B_NAME')?>:</label></td>
	<td class="adm-detail-content-cell-r"><input type="text" name="Name" id="Name" style="width:250px;" maxlength="255" value="<?=isset($f_Name) ? htmlspecialchars($f_Name) : ''?>" /></td>
</tr> 
<tr>
    <td class="adm-detail-content-cell-l"><span class="required">*</span> <label for="Code"><?=GetMessage('B_Code')?>:</label></td>
    <td class="adm-detail-content-cell-r"><input type="text" name="Code" id="Code" style="width:250px;" maxlength="255" value="<?=isset($f_Code) ? htmlspecialchars($f_Code) : ''?>" /></td>
</tr> 
<tr>
	<td class="adm-detail-content-cell-l"><label for="Sort"><?=GetMessage('B_SORT')?>:</label></td>
	<td class="adm-detail-content-cell-r"><input type="text" name="Sort" id="Sort" size="5" maxlength="5" value="<?=$f_Sort?>" /></td>
</tr>
<?
if ($itemId)
{    
	?>
    <tr>
    	<td class="adm-detail-content-cell-l"><?=GetMessage('B_CREATE_DATE')?>:</td>
    	<td class="adm-detail-content-cell-r"><?=SmartRealt_Common::DateToPHP($f_CreateDate)?></td>
    </tr>
    <tr>
    	<td class="adm-detail-content-cell-l"><?=GetMessage('B_UPDATE_DATE')?>:</td>
    	<td class="adm-detail-content-cell-r"><?=SmartRealt_Common::DateToPHP($f_UpdateDate)?></td>
    </tr>
	<?
}
?>
<tr>
	<td class="adm-detail-content-cell-l"><input type="checkbox" name="NEW_AFTER_SAVE" id="NEW_AFTER_SAVE" <?=(isset($_POST['NEW_AFTER_SAVE']) || ($_GET['AddNext']=='Y'))?"checked":""?> /></td>
	<td class="adm-detail-content-cell-r"><label for="NEW_AFTER_SAVE"><?=GetMessage('B_NEW_AFTER_SAVE')?></label></td>
</tr>

<?php
$tabControl->Buttons(array('back_url' => $sListPage . '?lang=' . LANGUAGE_ID));
$tabControl->End();
?>

</form>

<?php
if (count($eSaveErrors->GetMessages()) > 0)
{
	echo $tabControl->ShowWarnings('edit_rubric_group_form', $eSaveErrors);
}

CAdminMessage::ShowMessage(array('MESSAGE' => GetMessage('B_REQUIRED_FIELDS'), 'TYPE' => 'OK', 'HTML' => true));
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>