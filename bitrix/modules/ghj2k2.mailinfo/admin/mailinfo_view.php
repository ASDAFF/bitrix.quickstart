<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ghj2k2.mailinfo/prolog.php");
IncludeModuleLangFile(__FILE__);
$MOD_RIGHT = $APPLICATION->GetGroupRight("ghj2k2.mailinfo");
if($MOD_RIGHT<"R") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

ClearVars();

$message = null;
$bVarsFromForm = false;

if(CModule::IncludeModule('ghj2k2.mailinfo')):
	$CMailinfo = new CMailinfo();
	$ID = intval($ID);
	$event=$CMailinfo->getEvent($ID);
	
	$dateTime=getDate(strtotime($event['~DATE_EXEC']));
	$date=$dateTime['mday'].' '.GetMessage("calend_".substr(strtolower($dateTime['month']),0,3)).' '.$dateTime['year'];
	if(strlen(trim($date)))
		$date=' - '.$date;
	
	switch($event['SUCCESS_EXEC']) {
		case 'Y':
			$icon="main_user_success";
			break;
		case 'F':
			$icon="main_user_error";
			break;
		case 'P':
			$icon="main_user_loss";
			break;
		case '0':
			$icon="main_user_block";
			break;
		default:
			$icon="main_user_wait";
			break;
	}

	$aTabs = array(
		array("DIV" => "edit1", "TAB" => GetMessage("MAILINFO_CURR_TABS").$date, "ICON"=>$icon, "TITLE"=>GetMessage("curr_rates_rate_ex")),
	);
	$tabControl = new CAdminTabControl("tabControl", $aTabs);
	
	$APPLICATION->SetTitle(GetMessage("MAILINFO_INDEX_TITLE"));
	require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");
	
	$aContext = array(
	  array(
	    "ICON" => "btn_list",
	    "TEXT"=>GetMessage("MAIN_ADMIN_MENU_LIST"),
	    "LINK"=>"mailinfo_success.php?lang=".LANG,
	    "TITLE"=>GetMessage("MAIN_ADMIN_MENU_LIST")
	  ),
	);
	
	$context = new CAdminContextMenu($aContext);
	$context->Show();
	$tabControl->Begin();
	$tabControl->BeginNextTab();
	
	$allTemplates = $CMailinfo->getMailTemplateHTML($EVENT);
	$r=CAllEvent::GetSiteFieldsArray(); 
	  
	foreach($allTemplates as $key=>$template):

    $arr=CAllEvent::ExtractMailFields($event['~C_FIELDS']);  
    $subject=$template['~SUBJECT'];
    $to=$template['~EMAIL_TO'];
    $from=$template['~EMAIL_FROM'];
		$cc=$template['~CC'];
		$bcc=$template['~BCC'];
	
    foreach(array_merge($arr, $r) as $k=>$v) {
      $subject=str_replace('#'.$k.'#', $v, $subject);
      $to=str_replace('#'.$k.'#', $v, $to);
      $from=str_replace('#'.$k.'#', $v, $from);
  	  $cc=str_replace('#'.$k.'#', $v, $cc);
  	  $bcc=str_replace('#'.$k.'#', $v, $bcc);
    }?>
	  
    <tr>
      <td><?=GetMessage('MAILINFO_EVENT_NAME_TEMPLATE')?>:</td>
      <td><a href="/bitrix/admin/type_edit.php?EVENT_NAME=<?=$event["EVENT_NAME"]?>&lang=ru"><?=$template['EVENT_NAME']?></a></td>
    </tr>
    <tr>
      <td><?=GetMessage('MAILINFO_EVENT_FROM_TEMPLATE')?>:</td>
      <td><b><?=$from?></b></td>
    </tr>
    <tr>
      <td><?=GetMessage('MAILINFO_EVENT_TO_TEMPLATE')?>:</td>
      <td><b><?=$to?></b></td>
    </tr>    
    <tr>
      <td><?=GetMessage('MAILINFO_EVENT_SUBJECT_TEMPLATE')?>:</td>
      <td><b><?=$subject?></b></td>
    </tr>
    <?if(strlen(trim($cc))):?>
      <tr>
        <td><?=GetMessage('MAILINFO_EVENT_CC_TEMPLATE')?>:</td>
        <td><b><?=$cc?></b></td>
      </tr>
    <?endif;?>
    <?if(strlen(trim($bcc))):?>
      <tr>
        <td><?=GetMessage('MAILINFO_EVENT_BCC_TEMPLATE')?>:</td>
        <td><b><?=$bcc?></b></td>
      </tr>
    <?endif;?>
    <tr>
      <td colspan="2"><?=GetMessage('MAILINFO_CURR_TABS')?>:</td>
    </tr>
    <tr>
      <td colspan="2" style="background: #ffffff; font-size: 100%">
          <iframe frameborder="0" height="800px" width="100%" src="/bitrix/admin/mailinfo_view_iframe.php?EVENT=<?=$EVENT?>&ID=<?=$ID?>&KEY=<?=$key?>"></iframe>
      </td>
    </tr>
	
	<?endforeach;
	$tabControl->EndTab();
	$tabControl->Buttons(Array("disabled" => true, "back_url" =>"mailinfo_success.php?lang=".LANG.GetFilterParams("filter_")));
	$tabControl->End();
endif;
require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");
?>