<?
use \Bitrix\Main\Localization\Loc;
use \Bitrix\Main\Loader;
Loc::loadMessages(__FILE__);

$module_id = 'slam.easyform';
$MOD_RIGHT = $APPLICATION->getGroupRight($module_id);
if ($MOD_RIGHT < 'R')
	return;



$showRightsTab = true;

$arTabs = array(
    array(
        'DIV' => 'edit1',
        'TAB' => Loc::GetMessage('MSG_TAB_NAME'),
        'ICON' => '',
        'TITLE' => Loc::GetMessage('MSG_TAB_NAME')
    )
);

$arGroups = array(
    'MAIN' => array('TITLE' => Loc::GetMessage('MSG_GROUP_NAME_MAIN'), 'TAB' => 0),
    'MESSAGE' => array('TITLE' => Loc::GetMessage('MSG_GROUP_NAME'), 'TAB' => 0),
    'CAPTCHA' => array('TITLE' => Loc::GetMessage('MSG_GROUP_CAPTCHA'), 'TAB' => 0)
);

$arOptions = array(
    'EMAIL' => array(
        'GROUP' => 'MAIN',
        'TITLE' => Loc::GetMessage('MSG_EMAIL'),
        'TYPE' => 'STRING',
        'SORT' => '100',
    ),
    'SHOW_MESSAGE' => array(
        'GROUP' => 'MESSAGE',
        'TITLE' => Loc::GetMessage('MSG_SHOW_MESSAGE'),
        'TYPE' => 'CHECKBOX',
        'SORT' => '200',
    ),
    'NOTE' => array(
        'GROUP' => 'MESSAGE',
        'TITLE' => Loc::GetMessage('NOTE_TEXT'),
        'TYPE' => 'NOTE',
        'SORT' => '300'
    ),
    'MESSAGE_TEXT' => array(
        'GROUP' => 'MESSAGE',
        'TITLE' => Loc::GetMessage('MSG_MESSAGE_TEXT'),
        'TYPE' => 'TEXT',
        'COLS' => '57',
        'ROWS' => '5',
        'SORT' => '400',
        'NOTES' => Loc::GetMessage('MSG_MESSAGE_TEXT_NOPE')
    ),

    'CAPTCHA_KEY' => array(
        'GROUP' => 'CAPTCHA',
        'TITLE' => Loc::GetMessage('MSG_CAPTCHA_KEY'),
        'TYPE' => 'STRING',
        'SORT' => '100',
    ),
    'CAPTCHA_SECRET_KEY' => array(
        'GROUP' => 'CAPTCHA',
        'TITLE' => Loc::GetMessage('MSG_CAPTCHA_SECRET_KEY'),
        'TYPE' => 'STRING',
        'SORT' => '100',
    ),
);

$dbSites = \Bitrix\Main\SiteTable::getList(array(
    'filter' => array('ACTIVE' => 'Y')
));
$aSitesTabs = $arOptionsSite = array();
while ($site = $dbSites->fetch()) {
    $aSitesTabs[] = array('DIV' => 'opt_site_'.$site['LID'], "TAB" => '('.$site['LID'].') '.$site['NAME'], 'TITLE' => '('.$site['LID'].') '.$site['NAME'], 'LID' => $site['LID']);
    $arOptionsSite[$site['LID']] = $arOptions;
}

$arOptions = $arOptionsSite;


$module_id = 'slam.easyform';
if(Loader::IncludeModule($module_id)){
    $opt = new \Slam\Easyform\Options($module_id, $aSitesTabs, $arTabs, $arGroups, $arOptions, $showRightsTab);
    $opt->ShowHTML();
}
?>