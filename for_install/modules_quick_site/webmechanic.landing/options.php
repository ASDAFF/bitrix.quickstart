<?

$module_id = "webmechanic.landing";

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/include.php');
//IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/'.$module_id.'/options.php');
IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/options.php");
IncludeModuleLangFile(__FILE__);


$regions = array(
  GetMessage('webmechanic_region_msk'),
  GetMessage('webmechanic_region_spb'),
  GetMessage('webmechanic_region_adg'),
  GetMessage('webmechanic_region_alt'),
  GetMessage('webmechanic_region_altk'),
  GetMessage('webmechanic_region_amr'),
  GetMessage('webmechanic_region_arh'),
  GetMessage('webmechanic_region_ast'),
  GetMessage('webmechanic_region_bash'),
  GetMessage('webmechanic_region_bel'),
  GetMessage('webmechanic_region_brn'),
  GetMessage('webmechanic_region_bur'),
  GetMessage('webmechanic_region_vla'),
  GetMessage('webmechanic_region_vol'),
  GetMessage('webmechanic_region_volo'),
  GetMessage('webmechanic_region_vor'),
  GetMessage('webmechanic_region_dag'),
  GetMessage('webmechanic_region_evr'),
  GetMessage('webmechanic_region_zab'),
  GetMessage('webmechanic_region_iva'),
  GetMessage('webmechanic_region_ing'),
  GetMessage('webmechanic_region_irk'),
  GetMessage('webmechanic_region_kab'),
  GetMessage('webmechanic_region_kal'),
  GetMessage('webmechanic_region_kalm'),
  GetMessage('webmechanic_region_kalu'),
  GetMessage('webmechanic_region_kam'),
  GetMessage('webmechanic_region_kar'),
  GetMessage('webmechanic_region_kare'),
  GetMessage('webmechanic_region_kem'),
  GetMessage('webmechanic_region_kir'),
  GetMessage('webmechanic_region_kom'),
  GetMessage('webmechanic_region_kor'),
  GetMessage('webmechanic_region_kos'),
  GetMessage('webmechanic_region_kra'),
  GetMessage('webmechanic_region_krak'),
  GetMessage('webmechanic_region_kur'),
  GetMessage('webmechanic_region_kurs'),
  GetMessage('webmechanic_region_lip'),
  GetMessage('webmechanic_region_mag'),
  GetMessage('webmechanic_region_mar'),
  GetMessage('webmechanic_region_mor'),
  GetMessage('webmechanic_region_mur'),
  GetMessage('webmechanic_region_nem'),
  GetMessage('webmechanic_region_niz'),
  GetMessage('webmechanic_region_nov'),
  GetMessage('webmechanic_region_novo'),
  GetMessage('webmechanic_region_oms'),
  GetMessage('webmechanic_region_ore'),
  GetMessage('webmechanic_region_orl'),
  GetMessage('webmechanic_region_pen'),
  GetMessage('webmechanic_region_per'),
  GetMessage('webmechanic_region_pri'),
  GetMessage('webmechanic_region_psk'),
  GetMessage('webmechanic_region_pos'),
  GetMessage('webmechanic_region_rya'),
  GetMessage('webmechanic_region_sam'),
  GetMessage('webmechanic_region_sar'),
  GetMessage('webmechanic_region_sah'),
  GetMessage('webmechanic_region_saho'),
  GetMessage('webmechanic_region_sve'),
  GetMessage('webmechanic_region_sev'),
  GetMessage('webmechanic_region_smo'),
  GetMessage('webmechanic_region_sta'),
  GetMessage('webmechanic_region_tai'),
  GetMessage('webmechanic_region_tam'),
  GetMessage('webmechanic_region_tat'),
  GetMessage('webmechanic_region_tve'),
  GetMessage('webmechanic_region_tom'),
  GetMessage('webmechanic_region_tul'),
  GetMessage('webmechanic_region_tiv'),
  GetMessage('webmechanic_region_tum'),
  GetMessage('webmechanic_region_udm'),
  GetMessage('webmechanic_region_udm'),
  GetMessage('webmechanic_region_ula'),
  GetMessage('webmechanic_region_ust'),
  GetMessage('webmechanic_region_hab'),
  GetMessage('webmechanic_region_hak'),
  GetMessage('webmechanic_region_han'),
  GetMessage('webmechanic_region_che'),
  GetMessage('webmechanic_region_chec'),
  GetMessage('webmechanic_region_chu'),
  GetMessage('webmechanic_region_chuk'),
  GetMessage('webmechanic_region_yam'),
  GetMessage('webmechanic_region_yar')
);


$arOptions = array(
  'WEBMECHANIC_CREDIT_TITLE_EDIT' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_title'),
      'TYPE' => 'STRING',
      'DEFAULT' => GetMessage('webmechanic_landing_option_title_default'),
      'SIZE' => 71,
      'SORT' => '0',
   ),
  'WEBMECHANIC_CREDIT_ABOUT' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_company'),
      'TYPE' => 'TEXT',
      'DEFAULT' => GetMessage('webmechanic_landing_option_company_default'),
      'SORT' => '10',
      'COLS' => 70,
      'ROWS' => 30,
   ),
  'WEBMECHANIC_CREDIT_ADDRESS' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_address'),
      'TYPE' => 'STRING',
      'DEFAULT' => GetMessage('webmechanic_landing_option_address_default'),
      'SIZE' => 71,
      'SORT' => '20',
   ),
   'WEBMECHANIC_CREDIT_PHONE' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_phone'),
      'TYPE' => 'STRING',
      'DEFAULT' => '7 (977) 777-77-77',
      'SIZE' => 71,
      'SORT' => '30',
   ),
   'WEBMECHANIC_CREDIT_GIFT' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_form_descr'),
      'TYPE' => 'STRING',
      'DEFAULT' => GetMessage('webmechanic_landing_option_form_descr_default'),
      'SIZE' => 71,
      'SORT' => '40',
  ),
  'WEBMECHANIC_CREDIT_ACTION' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_present'),
      'TYPE' => 'TEXT',
      'DEFAULT' => GetMessage('webmechanic_landing_option_present_default'),
      'SORT' => '50',
      'COLS' => 70,
      'ROWS' => 30,
  ),
  'WEBMECHANIC_CREDIT_THANKS_MAIN' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_thanks'),
      'TYPE' => 'TEXT',
      'DEFAULT' => GetMessage('webmechanic_landing_option_thanks_default'),
      'SORT' => '60',
      'COLS' => 70,
      'ROWS' => 30,
   ),
  'WEBMECHANIC_CREDIT_TERM' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_term'),
      'TYPE' => 'TEXT',
      'DEFAULT' => GetMessage('webmechanic_landing_option_term_default'),
      'SORT' => '70',
      'COLS' => 70,
   ),
  'WEBMECHANIC_CREDIT_COMMENT' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_comment'),
      'TYPE' => 'TEXT',
      'DEFAULT' => GetMessage('webmechanic_landing_option_comment_default'),
      'SORT' => '90',
      'COLS' => 70,
      'ROWS' => 30,
   ),
  'WEBMECHANIC_CREDIT_COPY' => array(
      'GROUP' => 'MAIN',
      'TITLE' => GetMessage('webmechanic_landing_option_copy'),
      'TYPE' => 'STRING',
      'DEFAULT' => GetMessage('webmechanic_landing_option_copy_default'),
      'SIZE' => 71,
      'SORT' => '100',
   ),
  
  
  'WEBMECHANIC_CREDIT_MIN_AGE' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_min_age'),
      'TYPE' => 'INT',
      'DEFAULT' => 21,
      'SORT' => '120',
   ),
   'WEBMECHANIC_CREDIT_MAX_AGE' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_max_age'),
      'TYPE' => 'INT',
      'DEFAULT' => 60,
      'SORT' => '130',
      'NOTES' => '<div class="formula"></div>'
   ),
   'WEBMECHANIC_CREDIT_PHONE_CODE' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_phone_mask'),
      'TYPE' => 'STRING',
      'DEFAULT' => '+7 (999) 999-99-99',
      'SORT' => '140',
      'NOTES' => GetMessage('webmechanic_landing_option_phone_notes')
   ),
   'WEBMECHANIC_CREDIT_PERCENT' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_percent'),
      'TYPE' => 'INT',
      'DEFAULT' => 6,
      'SORT' => '70',
   ),
   'WEBMECHANIC_CREDIT_FPAY' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_fpay'),
      'TYPE' => 'INT',
      'DEFAULT' => 70,
      'SORT' => '80',
   ),
   'WEBMECHANIC_CREDIT_MIN_MONTH' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_min_month'),
      'TYPE' => 'INT',
      'DEFAULT' => 3,
      'SORT' => '90',
   ),
   'WEBMECHANIC_CREDIT_MAX_MONTH' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_max_month'),
      'TYPE' => 'INT',
      'DEFAULT' => 60,
      'SORT' => '100',
   ),
   'WEBMECHANIC_CREDIT_START' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_start'),
      'TYPE' => 'INT',
      'DEFAULT' => 6,
      'SORT' => '105',
   ),
   'WEBMECHANIC_CREDIT_REGION' => array(
      'GROUP' => 'CALC',
      'TITLE' => GetMessage('webmechanic_landing_option_region'),
      'TYPE' => 'MSELECT',
      'VALUES' => array(
        'REFERENCE_ID' => $regions, 
        'REFERENCE' => $regions,
      ),
      'SIZE' => 15,
      'SORT' => '150',
      'NOTES' => GetMessage('webmechanic_landing_option_region_notes')
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

//$opt = new CModuleOptions($module_id, $arTabs, $arGroups, $arOptions, $showRightsTab);
//$opt->ShowHTML();

?>