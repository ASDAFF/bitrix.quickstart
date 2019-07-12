<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_before.php');
IncludeModuleLangFile(__FILE__);

$B_RIGHT = $APPLICATION->GetGroupRight($module_id);

if ($B_RIGHT == 'D')
{
    $APPLICATION->AuthForm(GetMessage('ACCESS_DENIED'));
}

if (!CModule::IncludeModule($module_id))
{
    $APPLICATION->AuthForm(GetMessage('B_ERROR_LOAD_MODULE'));
}

$APPLICATION->SetTitle(GetMessage('B_TITLE'));

$sListPage = 'smartrealt_rubric_list.php';
$sEditPage = 'smartrealt_rubric_edit.php';

$sTableID = 'smartrealt_rubric';
/*if (!$by) $by='RubricSort';
if (!$order) $order='asc';  */
$oSort = new CAdminSorting($sTableID, 'Sort', 'asc');
$oAdmin = new CAdminList($sTableID, $oSort);   

$obRubric = new SmartRealt_Rubric();

$obRubricGroup = new SmartRealt_RubricGroup();
$rsRubricGroup = $obRubricGroup->GetList();
$arRubricGroupToSelect = array();
while ($arRubricGroup = $rsRubricGroup->Fetch())
    $arRubricGroupToSelect[$arRubricGroup['Id']] = $arRubricGroup['Name'];
mysql_data_seek($rsRubricGroup->result, 0);

$oSmartRealtCatalogType = new SmartRealt_CatalogElementType();
$rsSmartRealtCatalogType = $oSmartRealtCatalogType->GetList(); 
$arTypesForSelect = array(); 
while ($arSmartRealtCatalogType = $rsSmartRealtCatalogType->Fetch())
{
    $arTypesForSelect[$arSmartRealtCatalogType['Id']] = $arSmartRealtCatalogType['Name'];  
}
   
$arTransactionTypes = array(
    'SALE' => GetMessage('B_SALE'),
    'RENT' => GetMessage('B_RENT'),
    'DAILY_RENT' => GetMessage('B_DAILY_RENT'),
    );

$arEstateMarkets = array(
    '' => '',
    'PRIMARY' => GetMessage('B_PRIMARY'),
    'SECONDARY' => GetMessage('B_SECONDARY'),           
    );
    
$arSectionIds = array(
    '' => '',
    1 => GetMessage('B_SECTION_CITY_IN'),
    3 => GetMessage('B_SECTION_CITY_OUT'),
    4 => GetMessage('B_SECTION_COMMERCIAL'),
    5 => GetMessage('B_SECTION_FOREIGN'),
    6 => GetMessage('B_SECTION_OTHER'),
    );

$arFilterFields = array(
    'FindText', 'Name', 'Active', 'RubricGroupId',
    'CreateDateFrom', 'CreateDateTo', 'UpdateDateFrom', 'UpdateDateTo',
);
$oAdmin->InitFilter($arFilterFields);

// обработка редактирования в гриде
if($oAdmin->EditAction() && $B_RIGHT > 'R')
{
    foreach($FIELDS as $ID => $arFields)
    {
        if(!$oAdmin->IsUpdated($ID))
            continue;

        foreach ($arFields as $sField=>$sVal)
        {
            if ($sField == 'TypeId')
            {
                $arFields[$sField] = implode(';', $sVal);
            }
            else
            {
                $arFields[$sField] = trim($sVal);    
            }
            
        }
            
                                             
        $cAdminException = $obRubric->CheckFields($arFields);
        $arErrors = $cAdminException->GetMessages();
        
        if (count($arErrors) > 0)
        {
            $sErrors = '';
            
            foreach ($arErrors as $sError)
            {
                $sErrors .= $sError['text'] . '<br />';
            }
            
            $oAdmin->AddUpdateError($sErrors, $ID);
        }
        else
        {
            global $DB;
            $DB->StartTransaction();  
                       
            if ($obRubric->Add($arFields, $ID))
            {
                $DB->Commit();
            }
            else 
            {
                $DB->Rollback();
                $oAdmin->AddUpdateError(GetMessage('B_SAVE_ERROR'), $ID);
            }    
        }
    }
}

// обработка групповых действий
if(($arID = $oAdmin->GroupAction()) && $B_RIGHT > 'R')
{
    // отмечен чекбокс "Для всех"
    if($_REQUEST['action_target'] == 'selected')
    {
        $arID = array();
        $rsData = $obRubric->GetList(array('ALL'=>'Y'));
        
        while($arRes = $rsData->Fetch())
        {
            $arID[] = $arRes['Id'];
        }
    }
    
    foreach($arID as $ID)
    {
        if(strlen($ID) <= 0)
        {
            continue;
        }
        
        switch($_REQUEST['action'])
        {
            // удалить
            case 'delete':
                if ($B_RIGHT == 'W')
                {
                    if ($obRubric->IsDelete($ID))
                    {
                        if (!$obRubric->Delete($ID))
                        {
                            $oAdmin->AddGroupError(GetMessage("B_DELETE_ERROR"), $ID);
                        }
                    }
                    else
                    {
                        $oAdmin->AddGroupError(GetMessage("B_DELETE_IMPOSSIBLE"), $ID);
                    }
                }
                break;
            
            // активировать
            case 'activate':
                $arFields = array('Active' => 'Y');
                $obRubric->Add($arFields, $ID);
                break;
            
            // деактивировать
            case 'deactivate':
                $arFields = array('Active' => 'N');
                $obRubric->Add($arFields, $ID);
                break;
            
            // копировать    
            case 'copy':
                $obRubric->CopyObject($ID);
                break;
            
            default:
                break;
        }
    }
    // удаляем везде action чтобы не было повторного выполнения при переходе в режактировнаие в гриде
    unset($_REQUEST['action']);
    unset($_GET['action']);
    unset($_POST['action']);
}

// заполнение массива фильтра
$arFilter = array();
foreach ($arFilterFields as $sField)
{
    $arFilter[$sField] = $$sField;
}
if (strlen($arFilter['Name'])>0)
    $arFilter['Name'] = '%'.$arFilter['Name'].'%';

// получаем список
$arFilter['ALL'] = 'Y';
$rsRubric = $obRubric->GetList($arFilter, array($by=>$order));

$dbResultList = new CAdminResult($rsRubric, $sTableID);
$dbResultList->NavStart();

$oAdmin->NavText($dbResultList->GetNavPrint(GetMessage('B_PAGES')));
$oAdmin->AddHeaders(array(
    array('id' => 'Name', 'content' => GetMessage('B_NAME'), 'sort' => 'Name', 'default' => true),
    array('id' => 'Active', 'content' => GetMessage('B_ACTIVE'), 'sort' => 'Active', 'default' => true),
    array('id' => 'RubricGroupId', 'content' => GetMessage('B_RubricGroupId'), 'sort' => 'RubricGroupId', 'default' => true),
    array('id' => 'TypeName', 'content' => GetMessage('B_TypeName'), 'sort' => 'TypeName', 'default' => true),
    array('id' => 'TransactionType', 'content' => GetMessage('B_TransactionType'), 'sort' => 'TransactionType', 'default' => true),
    array('id' => 'SectionId', 'content' => GetMessage('B_SectionId'), 'sort' => 'SectionId', 'default' => true),
    array('id' => 'EstateMarket', 'content' => GetMessage('B_EstateMarket'), 'sort' => 'EstateMarket', 'default' => true),
    array('id' => 'TypeId', 'content' => GetMessage('B_TypeId'), 'sort' => 'TypeId', 'default' => true),
    array('id' => 'Code', 'content' => GetMessage('B_Code'), 'sort' => 'Code', 'default' => true),
    array('id' => 'Sort', 'content' => GetMessage('B_SORT'), 'sort' => 'Sort', 'default' => true),
    array('id' => 'CreateDate', 'content' => GetMessage('B_CREATE_DATE'), 'sort' => 'CreateDate', 'default' => true),
    array('id' => 'UpdateDate', 'content' => GetMessage('B_UPDATE_DATE'), 'sort' => 'UpdateDate', 'default' => true),
));

$arVisibleColumns = $oAdmin->GetVisibleHeaderColumns();

while ($arRubric = $dbResultList->NavNext(true, 'f_'))
{
    $row = &$oAdmin->AddRow($f_Id, $arRubric);
    
    $row->bReadOnly = ($B_RIGHT<='R');     
    
    $row->AddViewField('Name', $f_Name);
    if (!$row->bReadOnly)                            
        $row->AddInputField('Name', array('maxlength' => 255, 'size' => 30));
    
    $row->AddViewField('Active', ($f_Active=='Y')?GetMessage('B_YES'):GetMessage('B_NO'));
    if (!$row->bReadOnly)                            
        $row->AddCheckField('Active');
    
    $row->AddViewField('RubricGroupId', $f_RubricGroupId);
    if (!$row->bReadOnly)                            
        $row->AddSelectField('RubricGroupId', $arRubricGroupToSelect);

    $row->AddViewField('TypeName',  $f_TypeName);
    if (!$row->bReadOnly)
        $row->AddInputField('TypeName', array('maxlength' => 255, 'size' => 30));
        
    $row->AddViewField('TransactionType', $f_TransactionType);
    if (!$row->bReadOnly)                            
        $row->AddSelectField('TransactionType', $arTransactionTypes);
    
    $row->AddViewField('TypeId', SmartRealt_Common::GetListViewFieldHTML($arTypesForSelect, explode(';', $f_TypeId)));
    if (!$row->bReadOnly)
        $row->AddEditField('TypeId', SmartRealt_Common::GetListEditFieldHTML(sprintf('FIELDS[%d][TypeId][]', $f_Id), $arTypesForSelect, explode(';', $f_TypeId), array('size' => '5', 'multiple' => 'multiple')));

    $row->AddViewField('SectionId', $f_SectionId);
    if (!$row->bReadOnly)                            
        $row->AddSelectField('SectionId', $arSectionIds);

    $row->AddViewField('EstateMarket', $f_EstateMarket);
    if (!$row->bReadOnly)                            
        $row->AddSelectField('EstateMarket', $arEstateMarkets);
        
    $row->AddViewField('Code', $f_Code);
    if (!$row->bReadOnly)
        $row->AddInputField('Code', array('maxlength' => 255, 'size' => 30));
    
    $row->AddViewField('Sort', $f_Sort);
    if (!$row->bReadOnly)                            
        $row->AddInputField('Sort', array('maxlength' => 10, 'size' => 3));
    
    $row->AddViewField('CreateDate', SmartRealt_Common::FormatDateForList(SmartRealt_Common::DateToPHP($f_CreateDate)));
    $row->AddViewField('UpdateDate', SmartRealt_Common::FormatDateForList(SmartRealt_Common::DateToPHP($f_UpdateDate)));
    
    // действия в контекстном меню
    $arActions = Array();
    if ($B_RIGHT > 'R')
    {
        $arActions[] = array(
            'ICON'        => 'edit', 
            'TEXT'        => GetMessage('B_EDIT'), 
            'ACTION'    => $oAdmin->ActionRedirect($sEditPage . '?id=' . $f_Id . '&lang=' . LANGUAGE_ID), 
            'DEFAULT'    => true);
        $arActions[] = array(
            'ICON'        => 'copy', 
            'TEXT'        => GetMessage('B_COPY'), 
            'ACTION'    => $oAdmin->ActionDoGroup($f_Id, 'copy'));
    }
    
    if ($B_RIGHT == 'W')
    {
        $arActions[] = array(
            'ICON'        => 'delete', 
            'TEXT'        => GetMessage('B_DELETE'), 
            'ACTION'    => 'if(confirm(\'' . GetMessage('B_DELETE_CONFIRM', array('#NAME#' => addslashes($f_Name))) . '\')) ' . 
                            $oAdmin->ActionDoGroup($f_Id, 'delete'));
    }

    $row->AddActions($arActions);
}

// футер
$oAdmin->AddFooter(
    array(
        array(
            'title' => GetMessage('B_ALL_AMOUNT'),
            'value' => $rsRubric->SelectedRowsCount()
        ),
        array(
            'counter' => true,
            'title' => GetMessage('B_SELECTED_AMOUNT'),
            'value' => '0'
        ),
    )
);

// групповые действия
$arGroupActions = array();
if ($B_RIGHT > 'R')
{
    $arGroupActions['activate'] = GetMessage('B_ACTIVATE');
    $arGroupActions['deactivate'] = GetMessage('B_DEACTIVATE');
}
if ($B_RIGHT == 'W')
{
    $arGroupActions['delete'] = GetMessage('B_DELETE');
}
if (count($arGroupActions) > 0)
    $oAdmin->AddGroupActionTable($arGroupActions);

// контекстное меню
$aContext=array();
if ($B_RIGHT > 'R')
    $aContext[]= 
        array(
            'TEXT'    => GetMessage('B_ADD_BTN'),
            'LINK'    => $sEditPage . '?id=&lang=' . LANGUAGE_ID,
            'TITLE'    => GetMessage('B_ADD_BTN_TITLE'),
            'ICON'    => 'btn_new'
        );
         
$oAdmin->AddAdminContextMenu($aContext);
$oAdmin->CheckListMode();

// формируем фильтр
$oFilter = new CAdminFilter(
    $sTableID . '_filter',
    array(
        GetMessage('B_FILTER_NAME'), 
        GetMessage('B_FILTER_ACTIVE'),
        GetMessage('B_FILTER_RubricGroupId'),
        GetMessage('B_FILTER_CREATE_DATE'),
        GetMessage('B_FILTER_UPDATE_DATE'),
    )
);
                                                                                                 
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_admin_after.php');

SmartRealt_Common::CheckToken();
?>

<form name="<?=$sTableID?>_filter_form" method="GET" action="<?= $APPLICATION->GetCurPage()?>?">

<?php
$oFilter->Begin();
?>

<tr>
    <td><b><?=GetMessage('B_FILTER_FIND_TEXT')?>:</b></td>
    <td><input type="text" name="FindText" value="<?=$FindText?>" maxlength="255" style="width:100%;" /></td>
</tr>
<tr>
    <td><?=GetMessage('B_FILTER_NAME')?>:</td>
    <td><input type="text" name="Name" value="<?=$Name?>" maxlength="255" style="width:100%;" /></td>
</tr>    
<tr>
    <td><?=GetMessage('B_FILTER_ACTIVE')?>:</td>
    <td>
        <select name='Active' style="width: 100%;">
            <option value=''><?=GetMessage('B_ALL')?>
            <option value='Y' <?=($Active == 'Y') ? 'SELECTED' : ''?>><?=GetMessage('B_F_ACTIVE')?>
            <option value='N' <?=($Active == 'N') ? 'SELECTED' : ''?>><?=GetMessage('B_F_NO_ACTIVE')?>
        </select>
    </td>
</tr>
<tr>
    <td><?=GetMessage('B_FILTER_RubricGroupId')?>:</td>
    <td><?php echo SelectBox('RubricGroupId', $rsRubricGroup, GetMessage('B_ALL'), $RubricGroupId, 'style="width:100%;"');?></td>
</tr> 

<tr>
    <td><?=GetMessage('B_FILTER_CREATE_DATE')?>:</td>
    <td>
        <? echo CalendarPeriod('RubricCreateDateFrom', $CreateDateFrom, 'CreateDateTo', $CreateDateTo, $sTableID.'_filter_form');?> 
    </td>
</tr>
<tr>
    <td><?=GetMessage('B_FILTER_UPDATE_DATE')?>:</td>
    <td>
        <? echo CalendarPeriod('RubricUpdateDateFrom', $UpdateDateFrom, 'UpdateDateTo', $UpdateDateTo, $sTableID.'_filter_form');?> 
    </td>
</tr>

<?php
$oFilter->Buttons(
    array(
        'table_id'    => $sTableID, 
        'url'        => $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID, 
        'form'        => $sTableID . '_filter_form'
    )
);
$oFilter->End();
?>

</form>

<?php
$oAdmin->DisplayList();

require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/epilog_admin.php');
?>
