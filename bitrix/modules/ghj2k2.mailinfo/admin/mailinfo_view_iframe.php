<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/ghj2k2.mailinfo/prolog.php");
IncludeModuleLangFile(__FILE__);
$MOD_RIGHT = $APPLICATION->GetGroupRight("ghj2k2.mailinfo");
if($MOD_RIGHT<"R") $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(CModule::IncludeModule('ghj2k2.mailinfo')):
  
  $CMailinfo = new CMailinfo();
  $templates = $CMailinfo->getMailTemplateHTML($EVENT);
  $event = $CMailinfo->getEvent($ID);

  $r=CAllEvent::GetSiteFieldsArray('s1');
  $arr=CAllEvent::ExtractMailFields($event['~C_FIELDS']);
  $arr=array_merge($arr, $r);

  //foreach($templates as $k=>$template):

    if($templates[$KEY]['BODY_TYPE']=='html'){
      $template=$templates[$KEY]['~MESSAGE'];      
    }
    else {
      $template=htmlspecialchars($templates[$KEY]['~MESSAGE']);      
    }

    foreach($arr as $k=>$v) {
      $template=str_replace('#'.$k.'#', $v, $template);
    }

    echo '<pre>'.$template.'</pre>';
  //endforeach;
endif;?>