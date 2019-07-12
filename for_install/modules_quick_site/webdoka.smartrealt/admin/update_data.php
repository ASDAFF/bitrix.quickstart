<?php
/**
* ###################################
* # Copyright (c) 2012 SmartRealt   #
* # http://www.smartrealt.com       #
* # mailto:info@smartrealt.com      #
* ###################################
*/

$module_id = 'webdoka.smartrealt';

define('NO_AGENT_CHECK', true);

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
                                                      
$oCatalogElement = new SmartRealt_CatalogElement();
$oCatalogElementPhoto = new SmartRealt_CatalogElementPhoto();  

if (isset($_GET['action']))
{
    switch($_GET['action'])
    {
        case 'updateObjects':
            echo $oCatalogElement->LoadFromWebservice(strip_tags($_GET['update_date']), intval($_GET['offset']), 20, $_GET['deleted']=="true");
            break; 
        case 'updatePhotos':
            echo $oCatalogElementPhoto->LoadFromWebservice(strip_tags($_GET['update_date']), intval($_GET['offset']), 5, $_GET['deleted']=="true");
            break;
        case 'deleteObjects':
            $rsCatalogElement = $oCatalogElement->GetList(array("Limit" => 20));
            $oCatalogElement->ClearLastUpdateDate();
            $iCount = 0;
            while ($arCatalogElement = $rsCatalogElement->Fetch())
            {
                $oCatalogElement->Delete($arCatalogElement['Id'], false);
                
                $iCount++;
            }
            echo $iCount;
            break; 
        case 'deletePhotos':
            $rsCatalogElementPhoto = $oCatalogElementPhoto->GetList(array("Limit" => 20));
            $oCatalogElementPhoto->ClearLastUpdateDate();
            $iCount = 0;
            while ($arCatalogElementPhoto = $rsCatalogElementPhoto->Fetch())
            {
                $oCatalogElementPhoto->Delete($arCatalogElementPhoto['Id'], false);
                
                $iCount++;
            }
            echo $iCount;
            break;
        case 'clearObjects':
            $rsCatalogElement = $oCatalogElement->GetList(array("Deleted" => 'Y', "Limit" => 20));
            $iCount = 0;
            while ($arCatalogElement = $rsCatalogElement->Fetch())
            {
                $oCatalogElement->Delete($arCatalogElement['Id'], false);

                $iCount++;
            }
            echo $iCount;
            break;
        case 'clearPhotos':
            $rsCatalogElementPhoto = $oCatalogElementPhoto->GetList(array("Deleted" => 'Y', "Limit" => 20));
            $iCount = 0;
            while ($arCatalogElementPhoto = $rsCatalogElementPhoto->Fetch())
            {
                $oCatalogElementPhoto->Delete($arCatalogElementPhoto['Id'], false);

                $iCount++;
            }
            echo $iCount;
            break;
        default:
            break;  
    }
                              
    die();
}

$oWebService = SmartRealt_WebService::GetInstance();                                                                   

$sObjectsLastUpdateDate = $oCatalogElement->GetLastUpdateDate(); 

$iObjectsDBCount = $oCatalogElement->GetCount();
$iObjectsDeletedDBCount = $oCatalogElement->GetCount(array('Deleted' => 'Y'));
$bGetDeletedObjects =  $iObjectsDBCount > 0;
if ($oWebService)
    $iObjectsUpdateCount = $oWebService->GetObjectsCount($sObjectsLastUpdateDate, $bGetDeletedObjects); 

$sPhotosLastUpdateDate = $oCatalogElementPhoto->GetLastUpdateDate(); 

$iPhotosDBCount = $oCatalogElementPhoto->GetCount();
$iPhotosDeletedDBCount = $oCatalogElementPhoto->GetCount(array('Deleted' => 'Y'));
if ($oWebService) 
    $iPhotosUpdateCount = $oWebService->GetPhotosCount($sPhotosLastUpdateDate, $bGetDeletedObjects);

$arTabs = array(
    array('DIV' => 'update_data', 
        'TAB' => $itemId ? GetMessage('B_UPDATE_TAB') : GetMessage('B_UPDATE_TAB'), 
        'TITLE' => $itemId ? GetMessage('B_UPDATE_TITLE') : GetMessage('B_UPDATE_TITLE')),    
);

$tabControl = new CAdminTabControl("tabControl", $arTabs);
 
$APPLICATION->SetTitle(GetMessage('B_UPDATE_TITLE') );     
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

SmartRealt_Common::CheckToken();

if (!$oWebService)
{    
    $eConnectionErrors = new CAdminException();
        $eConnectionErrors->AddMessage(array(
                        'id' => 'connection_error',
                        'text' => SmartRealt_WebService::GetErrorMessage()
                    ));
    $message = new CAdminMessage(GetMessage('SMARTREALT_ALL_DATA_DOWNLOADED').':', $eConnectionErrors);
    echo $message->Show();
}
?>
<form name='form'>

<?php
$tabControl->Begin();
$tabControl->BeginNextTab();
?>

<col width="40%" />
<col />
<tr>
    <td colspan="2">
        <table width="100%" cellspacing="5" cellpadding="3" border="0"> 
            <tr>
                <td width="5%" valign="top"> </td>
                <td valign="top">
                    <table cellspacing="1" cellpadding="3" border="0">
                        <col width="270"/>
                        <col />
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_OBJECT_QUANTITY'); ?>:</td>
                            <td><?=$iObjectsDBCount?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_OBJECT_DELETED_QUANTITY'); ?>:</td>
                            <td><?=$iObjectsDeletedDBCount?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_OBJECT_LAST_UPDATE'); ?>:</td>
                            <td><?=$sObjectsLastUpdateDate?$DB->FormatDate($sObjectsLastUpdateDate, "YYYY-MM-DD HH:MI:SS"):'-'?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_OBJECT_UPDATE_QUANTITY'); ?>:</td>
                            <td><?=$iObjectsUpdateCount?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_PHOTO_QUANTITY'); ?>:</td>
                            <td><?=$iPhotosDBCount?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_PHOTO_DELETED_QUANTITY'); ?>:</td>
                            <td><?=$iPhotosDeletedDBCount?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_PHOTO_LAST_UPDATE'); ?>:</td>
                            <td><?=$sPhotosLastUpdateDate?$DB->FormatDate($sPhotosLastUpdateDate, "YYYY-MM-DD HH:MI:SS"):'-'?></td>
                        </tr>
                        <tr>
                            <td nowrap=""><?php echo GetMessage('SMARTREALT_PHOTO_UPDATE_QUANTITY'); ?>:</td>
                            <td><?=$iPhotosUpdateCount?></td>
                        </tr>
                    </table>
                </td>
            </tr>      
        </table>  
    </td>
</tr>
<tr>
    <td colspan="2">
        <div id="upd_install_div">
            <table border="0" cellspacing="1" cellpadding="3" width="100%" class="internal">
                <tr class="heading">
                    <td><B><?php echo GetMessage('SMARTREALT_DOWNLOAD'); ?></B></td>
                </tr>
                <tr>
                    <td valign="top">
                        <table border="0" cellspacing="5" cellpadding="3" width="100%">
                            <tr>
                                <td valign="top" width="5%">
                                </td>
                                <td valign="top">
                                    <script language="JavaScript">
                                    <!--
                                    var ns4 = (document.layers) ? true : false;
                                    var ie4 = (document.all) ? true : false;

                                    var txt = '';
                                    if (ns4)
                                    {
                                        txt += '<table border=0 cellpadding=0 cellspacing=0><tr><td>';
                                        txt += '<layer width="300" height="15" bgcolor="#365069" top="0" left="0"></layer>';
                                        txt += '<layer width="298" height="13" bgcolor="#ffffff" top="1" left="1"></layer>';
                                        txt += '<layer name="PBdoneD" width="298" height="13" bgcolor="#D5E7F3" top="1" left="1"></layer>';
                                        txt += '</td></tr></table>';
                                        txt += '<br>';
                                        txt += '<table border=0 cellpadding=0 cellspacing=0><tr><td>';
                                        txt += '<layer width="300" height="15" bgcolor="#365069" top="0" left="0"></layer>';
                                        txt += '<layer width="298" height="13" bgcolor="#ffffff" top="1" left="1"></layer>';
                                        txt += '<layer name="PBdone" width="298" height="13" bgcolor="#D5E7F3" top="1" left="1"></layer>';
                                        txt += '</td></tr></table>';
                                    }
                                    else
                                    {
                                        txt += '<div style="top:0px; left:0px; width:300px; height:15px; background-color:#365069; font-size:1px;">';
                                        txt += '<div style="position:relative; top:1px; left:1px; width:298px; height:13px; background-color:#ffffff; font-size:1px;">';
                                        txt += '<div id="PBdoneD" style="position:relative; top:0px; left:0px; width:0px; height:13px; background-color:#D5E7F3; font-size:1px;">';
                                        txt += '</div></div></div>';
                                        txt += '<br>';
                                        txt += '<div style="top:0px; left:0px; width:300px; height:15px; background-color:#365069; font-size:1px;">';
                                        txt += '<div style="position:relative; top:1px; left:1px; width:298px; height:13px; background-color:#ffffff; font-size:1px;">';
                                        txt += '<div id="PBdone" style="position:relative; top:0px; left:0px; width:0px; height:13px; background-color:#D5E7F3; font-size:1px;">';
                                        txt += '</div></div></div>';
                                    }
                                    document.write(txt);
                                    //-->
                                    </script>
                                    <br>
                                    <div id="install_progress_hint"></div>                              
                                </td>
                                <td valign="top" align="right">
                                    <input type="button" NAME="stop_updates" id="stop_button" value="<?php echo GetMessage('SMARTREALT_STOP'); ?>" onclick="StopAction()" style="display: none;">
                                </td>
                            </tr>
                            <tr>
                                <td valign="top" width="5%">
                                </td>
                                <td valign="top">
                                    <input type="button" value="<?php echo GetMessage('SMARTREALT_UPDATE_OBJECTS'); ?>" id="update_button" /> 
                                    <input type="button" value="<?php echo GetMessage('SMARTREALT_DELETE_OBJECTS'); ?>" id="delete_button" />
                                    <input type="button" value="<?php echo GetMessage('SMARTREALT_CLEAR_OBJECTS'); ?>" id="clear_button" />
                                </td>
                                <td valign="top"></td>
                            </tr>
                        </table>                             
                    </td>
                </tr>
            </table>
        </div>
    </td>
</tr>
<tr>
    <td colspan="2">
        
    </td>
</tr>


<?php
$tabControl->End();
?>

</form>

<script language="JavaScript">
<!--
var updateButton = document.getElementById("update_button"); 
var deleteButton = document.getElementById("delete_button");
var clearButton = document.getElementById("clear_button");
var stopButton = document.getElementById("stop_button");
var objectsLastUpdateDate = "<?=$sObjectsLastUpdateDate?>";     
var objectsUpdateCount = <?=intval($iObjectsUpdateCount)?>;     
var objectsDBCount = <?=intval($iObjectsDBCount)?>;
var objectsDeletedDBCount = <?=intval($iObjectsDeletedDBCount)?>;
var photosLastUpdateDate = "<?=$sPhotosLastUpdateDate?>";   
var photosUpdateCount = <?=intval($iPhotosUpdateCount)?>;     
var photosDBCount = <?=intval($iPhotosDBCount)?>;
var photosDeletedDBCount = <?=intval($iPhotosDeletedDBCount)?>;
var downloadDeletedObjects = <?=intval($iObjectsDBCount)>0?'true':'false'?>;     
var objectsLoaded = 0;
var photosLoaded = 0;
var totalLoaded = 0;
var connectError = '<?=addslashes(SmartRealt_WebService::GetErrorMessage())?>';   

updateButton.onclick = InstallUpdates;
deleteButton.onclick = DeleteAll;
clearButton.onclick = ClearDeleted;

var PBdone = (ns4) ? findlayer('PBdone', document) : (ie4) ? document.all['PBdone'] : document.getElementById('PBdone');
var PBdoneD = (ns4) ? findlayer('PBdoneD', document) : (ie4) ? document.all['PBdoneD'] : document.getElementById('PBdoneD');

var bStopAction = false;

if (photosUpdateCount == 0 && objectsUpdateCount == 0)
{
    SetProgressHint("<?php echo GetMessage('SMARTREALT_ALL_DATA_DOWNLOADED'); ?>");
    
    updateButton.disabled = true; 
}

if (photosDBCount == 0 && objectsDBCount == 0)
{
    deleteButton.disabled = true; 
}

if (connectError.length > 0)
{
    updateButton.disabled = true;  
}

if (photosDeletedDBCount == 0 && objectsDeletedDBCount == 0)
{
    clearButton.disabled = true;
}

function findlayer(name, doc)
{
    var i,layer;
    for (i = 0; i < doc.layers.length; i++)
    {
        layer = doc.layers[i];
        if (layer.name == name)
            return layer;
        if (layer.document.layers.length > 0)
            if ((layer = findlayer(name, layer.document)) != null)
                return layer;
    }
    return null;
}

function SetProgress(val)
{         
    if (val > 100)
        val = 100;
        
    if (ns4)
    {
        PBdoneD.clip.left = 0;
        PBdoneD.clip.top = 0;
        PBdoneD.clip.right = val*298/100;
        PBdoneD.clip.bottom = 13;
    }
    else
        PBdoneD.style.width = (val*298/100) + 'px';
} 

function SetTotalProgress(val)
{
    if (val > 100)
        val = 100;
    
    if (ns4)
    {
        PBdone.clip.left = 0;
        PBdone.clip.top = 0;
        PBdone.clip.right = val * 298 / 100;
        PBdone.clip.bottom = 13;
    }
    else
        PBdone.style.width = (val * 298 / 100) + 'px';          
}

function SetProgressHint(val)
{
    var installProgressHintDiv = document.getElementById("install_progress_hint");
    installProgressHintDiv.innerHTML = val;
}

function InstallUpdates()
{
    objectsLoaded = 0;
    photosLoaded = 0; 
    totalLoaded = 0;
    
    ShowWaitWindow();
    
    action =  'updateObjects'; 
    
    updateButton.disabled = true;
    deleteButton.disabled = true;
    clearButton.disabled = true;
    stopButton.style["display"] = ""; 
    
    SetProgressHint('<?php echo GetMessage('SMARTREALT_DOWNLOAD_OBJECTS'); ?>');
    
    CHttpRequest.Action = function(result)
    {
        UpdateAction(result);
    }             

    CHttpRequest.Send('?action=' + action + "&update_date=" + objectsLastUpdateDate + "&deleted=" + downloadDeletedObjects); 
} 

function UpdateAction(result)
{
    if (bStopAction)
    {
        SetProgressHint("<?php echo GetMessage('SMARTREALT_DOWNLOAD_CANCELED'); ?>");
        CloseWaitWindow();
        return;
    }
    
    if (result.length > 0 && parseInt(result) >= 0)
    {
        var offset = 0;
        
        switch (action)
        {
            case 'updateObjects':
                objectsLoaded += parseInt(result);
                totalLoaded += parseInt(result);
                offset = objectsLoaded;
                SetProgress(objectsLoaded/objectsUpdateCount*100);
                lastUpdateDate = objectsLastUpdateDate;
                break;
            case 'updatePhotos':
                photosLoaded += parseInt(result);   
                totalLoaded += parseInt(result);
                offset = photosLoaded;
                SetProgress(photosLoaded/photosUpdateCount*100);
                lastUpdateDate = photosLastUpdateDate;                       
                break;
            default:
                return;
        }
        
        SetTotalProgress(totalLoaded/(photosUpdateCount + objectsUpdateCount)*100);
        
        if (action == 'updateObjects' && (objectsLoaded >= objectsUpdateCount))
        {
            action = 'updatePhotos';
            offset = 0;
            SetProgressHint("<?php echo GetMessage('SMARTREALT_DOWNLOAD_PHOTOS'); ?>");
        }
        
        if (!bStopAction)
        {
            if (totalLoaded < (photosUpdateCount + objectsUpdateCount))
            {
                CHttpRequest.Send('?action=' + action + "&update_date=" + lastUpdateDate + "&offset=" + offset + "&deleted=" + downloadDeletedObjects);    
            }
            else
            {                  
                SetProgressHint("<?php echo GetMessage('SMARTREALT_DOWNLOAD_COMPLITE'); ?>");
                stopButton.style["display"] = "none";
                CloseWaitWindow();
                setTimeout(function(){
                    window.location.reload();
                    }, 1000);
            }
        } 
    }
}

function DeleteAll()
{
    if (confirm("<?php echo GetMessage('SMARTREALT_DELETE_CONFIRM'); ?>"))
    {
        ShowWaitWindow();
        action =  'deleteObjects';
        
        objectsLoaded = 0;
        photosLoaded = 0; 
        totalLoaded = 0;
        
        updateButton.disabled = true;
        deleteButton.disabled = true;
        clearButton.disabled = true;
        //stopButton.style["display"] = "";
    
        SetProgressHint("<?php echo GetMessage('SMARTREALT_DELETING_OBJECTS'); ?>");
        
        CHttpRequest.Action = function(result)
        {
            DeleteAction(result);
        }             

        CHttpRequest.Send('?action=' + action); 
    }
}

function DeleteAction(result)
{
    if (bStopAction)
    {
        SetProgressHint("<?php echo GetMessage('SMARTREALT_DELETE_CANCEL'); ?>");
        CloseWaitWindow();
        return;
    }

    if (result.length > 0 && parseInt(result) >= 0)
    {
        switch (action)
        {
            case 'deleteObjects':
                objectsLoaded += parseInt(result);
                totalLoaded += parseInt(result);
                SetProgress(objectsLoaded/objectsDBCount*100);
                break;
            case 'deletePhotos':
                photosLoaded += parseInt(result);
                totalLoaded += parseInt(result);
                SetProgress(photosLoaded/photosDBCount*100);
                break;
            default:
                return;
        }

        SetTotalProgress(totalLoaded/(photosDBCount + objectsDBCount)*100);

        if (action == 'deleteObjects' && objectsLoaded >= objectsDBCount)
        {
            action = 'deletePhotos';
            offset = 0;
            SetProgressHint("<?php echo GetMessage('SMARTREALT_DELETING_PHOTOS'); ?>");
        }

        if (!bStopAction)
        {
            if (totalLoaded < (photosDBCount + objectsDBCount))
            {
                CHttpRequest.Send('?action=' + action);
            }
            else
            {
                SetProgressHint("<?php echo GetMessage('SMARTREALT_DELETE_COMPLITE'); ?>");
                stopButton.style["display"] = "none";
                CloseWaitWindow();

                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }
        }
    }
}

function ClearDeleted()
{
    if (confirm("<?php echo GetMessage('SMARTREALT_CLEAR_CONFIRM'); ?>"))
    {
        ShowWaitWindow();
        action =  'clearObjects';

        objectsLoaded = 0;
        photosLoaded = 0;
        totalLoaded = 0;

        updateButton.disabled = true;
        deleteButton.disabled = true;
        clearButton.disabled = true;
        //stopButton.style["display"] = "";

        SetProgressHint("<?php echo GetMessage('SMARTREALT_CLEARING_OBJECTS'); ?>");

        CHttpRequest.Action = function(result)
        {
            ClearAction(result);
        }

        CHttpRequest.Send('?action=' + action);
    }
}

function ClearAction(result)
{
    if (bStopAction)
    {
        SetProgressHint("<?php echo GetMessage('SMARTREALT_CLEAR_CANCEL'); ?>");
        CloseWaitWindow();
        return;
    }

    if (result.length > 0 && parseInt(result) >= 0)
    {
        switch (action)
        {
            case 'clearObjects':
                objectsLoaded += parseInt(result);
                totalLoaded += parseInt(result);
                SetProgress(objectsLoaded/objectsDeletedDBCount*100);
                break;
            case 'clearPhotos':
                photosLoaded += parseInt(result);
                totalLoaded += parseInt(result);
                SetProgress(photosLoaded/photosDeletedDBCount*100);
                break;
            default:
                return;
        }

        SetTotalProgress(totalLoaded/(photosDeletedDBCount + objectsDeletedDBCount)*100);

        if (action == 'clearObjects' && objectsLoaded >= objectsDeletedDBCount)
        {
            action = 'clearPhotos';
            offset = 0;
            SetProgressHint("<?php echo GetMessage('SMARTREALT_CLEARING_PHOTOS'); ?>");
        }

        if (!bStopAction)
        {
            if (totalLoaded < (photosDeletedDBCount + objectsDeletedDBCount))
            {
                CHttpRequest.Send('?action=' + action);
            }
            else
            {
                SetProgressHint("<?php echo GetMessage('SMARTREALT_CLEAR_COMPLETE'); ?>");
                stopButton.style["display"] = "none";
                CloseWaitWindow();

                setTimeout(function(){
                    window.location.reload();
                }, 1000);
            }
        }
    }
}
    
function StopAction()
{
    bStopAction = true;
    stopButton.disabled = true;
    ShowWaitWindow();
}
//-->
</script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>
