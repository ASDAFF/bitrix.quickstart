<?
$module_id = 'sotbit.instagram';

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
IncludeModuleLangFile(__FILE__);
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include/CModuleOptions.php');
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
CModule::IncludeModule("sotbit.instagram");
$showRightsTab = false;
echo '<div class="adm-info-message-wrap">
<div class="adm-info-message">';
echo GetMessage('sotbit_instagram_descr');
echo '</div></div>';

if($REQUEST_METHOD=="POST" && strlen($RestoreDefaults)>0 && check_bitrix_sessid())
{
		COption::RemoveOption($module_id);
		$z = CGroup::GetList($v1="id",$v2="asc", array("ACTIVE" => "Y", "ADMIN" => "N"));
		while($zr = $z->Fetch())
			$APPLICATION->DelGroupRight($module_id, array($zr["ID"]));

        if((strlen($Apply) > 0) || (strlen($RestoreDefaults) > 0))
			LocalRedirect($APPLICATION->GetCurPage()."?mid=".urlencode($module_id)."&lang=".urlencode(LANGUAGE_ID)."&back_url_settings=".urlencode($_REQUEST["back_url_settings"]));
		else
			LocalRedirect($_REQUEST["back_url_settings"]);
}




$arTabs = array(
   array(
      'DIV' => 'edit1',
      'TAB' => GetMessage('sotbit_instagram_edit1'),
      'ICON' => '',
      'TITLE' => GetMessage('sotbit_instagram_edit1'),
      'SORT' => '10'
   ),

     
);
                               
$arGroups = array(
   'OPTION_10' => array('TITLE'=>GetMessage('sotbit_instagram_edit1'), 'TAB' => 0),

);

$arOptions = array(
    'LOGIN' => array(
      'GROUP' => 'OPTION_10',
      'TITLE' => GetMessage('sotbit_instagram_login'),
      'TYPE' => 'STRING',
      'DEFAULT' => '',
      'SORT' => '10',
      'REFRESH' => 'N',
      'SIZE' => '30'
   ),
    'CLIENT_ID' => array(
      'GROUP' => 'OPTION_10',
      'TITLE' => GetMessage('sotbit_instagram_client_id'),
      'TYPE' => 'STRING',
      'DEFAULT' => '',
      'SORT' => '10',
      'REFRESH' => 'N',
      'SIZE' => '30'
   ),


       
);


/*
Конструктор класса CModuleOptions
$module_id - ID модуля
$arTabs - массив вкладок с параметрами
$arGroups - массив групп параметров
$arOptions - собственно сам массив, содержащий параметры
$showRightsTab - определяет надо ли показывать вкладку с настройками прав доступа к модулю ( true / false )
*/

$opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
$opt->ShowHTML();

?>